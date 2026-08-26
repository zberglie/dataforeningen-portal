# Arkitekturnotat: ressursportalen på Craft CMS

**Dato:** 12. august 2026 · **Forutsetning:** Craft velges som CMS (antakelse — dataforeningen.no
kjører Craft i dag, bygget av Værsågod på Servd-hosting). Målet med notatet: bygge slik at
ingenting må bygges om når StyreWeb-svaret kommer, når Figma-designet kommer, eller når
fase 2 starter.

---

## 1. Bærende prinsipp: tre bevegelige deler bak tre stabile kontrakter

Alt som er usikkert i prosjektet ligger i tre eksterne avhengigheter. Portalen skal aldri
kjenne dem direkte — bare kontraktene:

| Bevegelig del | Stabil kontrakt | Implementasjoner |
|---|---|---|
| **Identitet** (StyreWeb-SSO? Entra External ID?) | Portalen er en standard **OIDC-klient** (authorization code + PKCE) | StyreWeb-IdP *eller* Entra External ID — byttes i konfig, ikke kode. Alle fire utfall i beslutningstreet (docs/05 kap. 6) plugger inn her |
| **Medlemsstatus** (API? eksport?) | Intern tjeneste: `aktivtMedlem(epost/medlemsnr): bool` | StyreWeb-API-oppslag *eller* eksport-tabell med synkjobb. Kalles ved innlogging + revalidering med TTL |
| **Dokumenter** (SharePoint) | Intern tjeneste: `hentDokument(ressurs): [innhold, mime, filnavn]` | Microsoft Graph (produksjon) *eller* mock (utvikling). Nøyaktig mønsteret WordPress-piloten allerede demonstrerer |

Blir dette liggende fast, er resten av portalen (innholdsmodell, maler, søk, admin) immun
mot alle avklaringene som gjenstår.

```
Medlem ── dataforeningen.no (Craft, Værsågod) ── «Logg inn»-knapp (KR-01)
             │
             ▼
   portal.dataforeningen.no (egen Craft Pro-instans — vår)
             │  OIDC ──► IdP: StyreWeb hvis mulig, ellers Entra External ID
             │             claims: navn, e-post, (medlemsnr)
             │  ved innlogging ──► aktivtMedlem()? ──► StyreWeb-API / eksport
             │
             ├── innhold: seksjonene Område → Temaside → Ressurs (+ søk)
             └── /dokument/<id> ──► DokumentKilde ──► Graph (Sites.Selected)
                                                        └─► én dedikert SharePoint-site
```

## 2. Den avgjørende tidlige beslutningen: samme installasjon eller egen instans

Dette er den ene beslutningen som er dyr å angre, og den må tas før første mal skrives:

| | A: Inn i dataforeningen.no-installasjonen | B: Egen Craft-instans på `portal.dataforeningen.no` |
|---|---|---|
| KR-01 (innlogging starter på DND.no) | Naturlig | Like greit — knapp på hovedsiden |
| Kobling til Værsågods kodebase og deploy-løp | Full — hver portalendring går gjennom deres pipeline/avtale | Ingen — vi eier repo og tempo |
| Sikkerhets-blastradius | Feil i portalkode treffer hovednettstedet | Isolert |
| Design-gjenbruk | Direkte | Design-tokens kopieres (samme fonter/farger) |
| Kostnad | Ingen ny lisens, men avtaletid hos byrå | Egen Craft Pro-lisens + hosting |

**Anbefaling: B — egen instans på subdomene**, med mindre DND selv eier hovedsidens repo
og deploy fullt ut. Begrunnelse: vi bygger og forvalter internt (B10); å være gjest i et
byrås kodebase gir friksjon i begge retninger, og en medlemsportal med innlogging og
dokumentproxy har en annen risikoprofil enn et publiseringsnettsted. Samme eTLD
(dataforeningen.no) gjør lenking og ev. fremtidig SSO-deling ryddig.
**Avklar med Værsågod tidlig** (én e-post): eierskap til hovedrepoet, design-tokens,
og om de vil ha portalen inn eller er komfortable med subdomene.

## 3. Innholdsmodellen i Craft — og hvorfor den blir «riktig første gang» der

- **Seksjoner:** `omrader` (structure, nivå 1), `temasider` (structure eller channel med
  entries-relasjon til område), `ressurser` (channel med entries-relasjon til temaside).
  Ressurser skal være **egne entries, ikke matrix-blokker** — da får de egen URL
  (nivå 4 dokumentvisning, KR-17), egne søketreff (KR-24) og egen metadata (KR-25).
- **Felter:** ressurs: `type` (dropdown: PDF/Word/Excel/PowerPoint/Forms/Lenke),
  `dokumentkilde` (Graph drive-item-ID — mer robust enn URL), `sokeord`, `beskrivelse`.
  Temaside: `innholdseier`, `relaterteRessurser` (entries-felt, KR-15). «Sist oppdatert»
  er gratis (`dateUpdated`).
- **Project Config er nøkkelmekanismen:** hele innholdsmodellen ligger som YAML i git
  (`config/project/`). Modellendringer gjøres lokalt, code-reviewes og deployes —
  aldri klikkes direkte i prod. Det er dette som gjør at modellen bare bygges én gang.
- Redaktøropplevelsen dekker KR-10–15/28–30 native: nye områder/temasider er entries,
  skjul = disabled, rekkefølge = dra i structure, live preview følger med.

## 4. Innlogging og medlemskap (KR-01–08, NFR-06)

- **Craft Pro er påkrevd** — medlemsinnlogging på frontend (brukerkontoer) finnes ikke i
  gratisutgaven. (~$299 første år, deretter årlig fornyelse — sjekk gjeldende priser.)
- OIDC via etablert plugin (f.eks. Verbb Social Login med egendefinert OIDC-leverandør);
  offentlig registrering avslått. Brukere opprettes **JIT ved første OIDC-innlogging**
  med minimal profil (navn, e-post, medlemsnr-claim) — ingenting annet lagres (NFR-06).
- Ved hver innlogging (og med TTL, f.eks. 24 t): `aktivtMedlem()` — nei → logg ut +
  tydelig melding (KR-04). Sesjonslengde konfigureres for auto-utlogging (KR-06).
- «Mitt medlemskap» og faggruppelenker (KR-07–08) er rene dyplenker til StyreWeb —
  innstillinger i en global set, redigerbart uten deploy.

## 5. Dokumentproxy som Craft-modul (KR-16–19)

- Egen modul (`modules/dokumentproxy`) med controller-rute `/dokument/<id>`:
  sjekk innlogging → slå opp entry → `DokumentKilde::hent()` → stream med riktige headere.
- Graph-implementasjonen: app-registrering i DNDs tenant med **`Sites.Selected`** —
  appen får tilgang til nøyaktig én dedikert SharePoint-site for portaldokumenter,
  ikke hele tenanten. (Viktig både for sikkerhet og GDPR-fortellingen.)
- Office-formater: `?format=pdf` for forhåndsvisning i portalen (pdf.js), original
  streames for nedlasting. Cache i Craft-cachen (kort TTL) mot Graph-throttling.
- Mock-implementasjonen fra WordPress-piloten porteres først — utvikling og demo trenger
  aldri ekte SharePoint.

## 6. Miljøer, pipeline og drift — resten av «riktig første gang»

- **Lokalt:** ddev (de facto-standard for Craft). **Miljøer:** dev → staging → prod fra
  dag én; staging er der Hege tester redaktørflyt og WCAG sjekkes før go-live.
- **Git-disiplin:** alt i repo (templates, modul, project config, seed); secrets i `.env`;
  deploy = git push + `craft up`. Ingen endringer rett i prod.
- **Hosting:** Servd (Craft-spesialisert, brukes av hovedsiden i dag, EU-region) er
  friksjonsfritt; alternativt EU/EØS-VPS eller Azure (nonprofit-tilskuddet $2 000/år).
  TLS, daglig DB-backup, oppetidsovervåking (gratis, NFR-13) fra første dag.
- **Søk:** Crafts innebygde søk over entries (titler + felter, inkl. `sokeord`) dekker
  KR-23–25 for MVP. Oppgraderingssti til fulltekst senere uten arkitekturendring.
- **Kvalitetsgrepene fra gap-analysen kap. 8** legges inn som småjobber i modulen:
  ukentlig lenke-/kilde-sjekk mot Graph, Plausible/Matomo, «sist oppdatert» + eier i malene.

## 7. Hva som gjenbrukes fra det som allerede er bygget

| Fra | Til Craft |
|---|---|
| Prototypens CSS/design (`prototype/index.html`) | Twig-maler — struktur og klassenavn overføres nesten 1:1 |
| WP-pilotens innholdsmodell | Seksjons-/feltoppsettet i kap. 3 (samme modell, annet vokabular) |
| WP-pilotens dokumentproxy-mock (inkl. PDF-generatoren) | `DokumentKilde`-mock i modulen |
| Seed-dataene (11 områder / 30 temasider / 66 ressurser) | Migrering/seed-kommando i modulen |

WordPress-piloten beholdes som referanse til Craft-varianten er verifisert like langt —
så tas plattformbeslutningen endelig (B10-hypotesen oppdateres i beslutningsloggen).

## 8. Byggerekkefølge

1. **Avklaringer i parallell (mennesker):** Værsågod (repo-eierskap, design-tokens,
   subdomene) · StyreWeb-møtet (docs/05 kap. 6) · app-registrering med Sites.Selected.
2. Repo + ddev + Craft Pro + project config med hele innholdsmodellen (kap. 3).
3. OIDC mot en **Entra External ID-testtenant** — virker uansett StyreWeb-utfall;
   StyreWeb-IdP byttes inn i konfig hvis møtet åpner for det.
4. Dokumentproxy-modul med mock → deretter Graph mot en test-SharePoint-site.
5. Twig-maler fra prototypen + seed. Redaktørtest med Hege på staging (NFR-05/T-04).
6. WCAG-gjennomgang, produksjonsoppsett (kap. 6), akseptansetestene T-01–T-08 fra
   kravspecen kap. 19 som sjekkliste — go-live.

---

*Notat utarbeidet med Claude Code 12.08.2026. Bygger på docs/05 (gap-analyse),
WordPress-piloten (`wordpress-pilot/`) og researchrapport v2. Craft-lisenspriser og
plugin-valg verifiseres mot gjeldende dokumentasjon før steg 2.*
