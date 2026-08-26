# Pilotmiljø: Dataforeningens ressursportal (WordPress)

**URL:** http://172.30.112.84:8082 (fra nettverket serveren står i)
**Formål:** validere WordPress-arbeidshypotesen (beslutningslogg B10) mot kravspec
ressursportal v1.2 før forpliktelse. Satt opp 12.08.2026.

## Innlogginger

Hele portalen krever innlogging (plassholder for StyreWeb-SSO/Entra External ID).

| Bruker | Passord | Rolle |
|---|---|---|
| `admin` | `DndPilot2026!` | Administrator |
| `hege` | `DndPilot2026!` | Redaktør — demonstrerer NFR-05 (innhold uten utvikler) |
| `kari` | `DndPilot2026!` | Abonnent — vanlig medlem |

Adminpanel: http://172.30.112.84:8082/wp-admin (menyene «Ressursområder»,
«Temasider» og «Ressurser»).

## Drift

```bash
cd /home/ubuntu/prosjekter/dataforeningen-portal/wordpress-pilot
docker compose up -d          # start (data i named volumes)
docker compose down           # stopp
docker compose down -v        # slett alt inkl. databasen
docker compose run --rm cli wp <kommando>   # WP-CLI
docker compose run --rm cli wp eval-file /seed.php   # reetabler demoinnhold (idempotent)
```

Bytte adresse: `docker compose run --rm cli wp option update home "http://NY:8082" && docker compose run --rm cli wp option update siteurl "http://NY:8082"`

## Hva piloten består av

| Del | Fil(er) | Hva |
|---|---|---|
| Tema `dnd-ressursportal` | `wp-content/themes/dnd-ressursportal/` | Prototypens design portert til WP-maler: forside (hero, søk, kortgrid), område-, temaside-, ressurs- og søkevisning, brødsmulesti |
| Plugin `dnd-portal` | `wp-content/plugins/dnd-portal/` | Innholdstypene område/temaside/ressurs med relasjons-metabokser, søk på tvers (KR-23–25), innloggingsport, adminkolonner |
| Plugin `dnd-dokumentproxy` | `wp-content/plugins/dnd-dokumentproxy/` | Proxy-mønsteret (KR-16–19): `?dnd_dokument=<id>` streamer dokument til innlogget bruker. Pilot: generert plassholder-PDF. Produksjon: bytt **én funksjon** (`dnd_proxy_hent_dokument()`) til Graph-kall |
| Seed | `seed.php` | Kravspecens 11 ressursområder → 30 temasider → 66 ressurser + 4 sider + demobrukere. Idempotent |

## Verifisert 12.08.2026

- Innloggingsport: uinnlogget → wp-login (302); dokumentproxy avviser også uinnlogget
- Forside med alle 11 områder; søk «budsjettmal» → «Budsjettmal for konferanser» (T-07)
- Brukerreise 7.1 på tre klikk: forside → område → temaside → «Mal for protokoll» (T-03)
- Proxy streamer gyldig PDF (verifisert med pypdf), riktig Content-Type/Disposition
- Redaktørflate: `hege` kan opprette/endre/skjule områder og temasider uten kode (T-04)

## Kravdekning i piloten

| Krav | Status |
|---|---|
| KR-09 forside, KR-10–15 admin/struktur, KR-20–22 navigasjon, KR-23–25 søk, KR-26 Forms (mock), KR-27 booking-veiledning, KR-31–32 responsivt | Demonstrert |
| KR-01–05 innlogging | Port på plass; lokal innlogging til StyreWeb-avklaringen er tatt (OIDC-plugin byttes inn) |
| KR-16–19 dokumenter | Proxy-mønster på plass med mock; Graph-integrasjon krever app-registrering i DNDs tenant |
| KR-07–08 Mitt medlemskap | Side med forklaring; dyplenke venter på StyreWeb-URL-struktur |
| NFR-06 (ingen medlemsdata) | Piloten lagrer kun WP-brukere; i produksjon holdes profilen minimal (navn + e-post fra SSO-claims) |

## Neste steg (i rekkefølge)

1. **StyreWeb-møtet** (agenda i `docs/05-gapanalyse-ressursportal.md` kap. 6) → velg
   innloggingsvariant fra beslutningstreet; installer OIDC-plugin deretter.
2. **Graph-dokumentproxy:** app-registrering i DNDs tenant (Files.Read.All app-tillatelse
   mot avgrenset SharePoint-site), bytt `dnd_proxy_hent_dokument()`, legg på caching.
   Office-forhåndsvisning via `?format=pdf` + pdf.js-visning i `single-ressurs.php`.
3. **Figma:** når Yvonnes design foreligger, oppdateres temaet (CSS-tokens er samlet i
   `:root` i style.css).
4. **Produksjonsoppsett:** EU/EØS-host eller Azure (tilskudd), TLS, backup av database,
   oppetidsovervåking, WCAG-gjennomgang (trolig lovpålagt — gap-analysen kap. 7).
5. Kvalitetsgrep fra gap-analysen kap. 8: lenkesjekk-cron mot SharePoint, Plausible/Matomo.

**Pilot-forbehold:** HTTP uten TLS, demopassord, e-post ikke konfigurert — skal ikke
eksponeres mot internett i denne tilstanden.
