# Gap-analyse: kravspesifikasjonen mot utredningen

**Dato:** 12. august 2026.
**Grunnlag:** e-post fra Hege Svendsen 12.08.2026 (`/home/ubuntu/delt/DND portal/Kravspek.eml`) med tre vedlegg:

| Vedlegg | Hva | Status |
|---|---|---|
| Kravspesifikasjon **Ressursportal** v1.2 (juni 2026), 15 s. | Styringsdokumentet. Sendbar spec til Team Experis Bergen (pro bono-utviklingsteam) | **Gjeldende kravbilde** |
| Kravspesifikasjon medlemssystem (16.03.2026), 45 s. | Spec for nytt medlemssystem (Dynamics/Power Platform-basert) | **Forkastet** — DND valgte StyreWeb i stedet. Nyttig kontekst |
| Medlemsportal.pdf | Heges egen designskisse av forsiden (1280×4234 px) | Innspill til Yvonnes Figma-arbeid |

Konteksten fra e-posten: Hege ber om gjennomgang **før** møte med Team Bergen, foreslår
møte med StyreWeb først («for å se hva du kan få fra de»), og Yvonne eier design/Figma.
Skissen «trenger ikke være helt likt eller så komplisert. Viktig med riktig innlogging osv.»

---

## 1. Hovedfunn

1. **Kravspecen beskriver et annet produkt enn utredningen antok.** Dag 1-utredningen
   handlet om *selvbetjeningsportal for faggrupper* (finne grupper, melde seg inn/ut,
   interesseforslag, fagstyre-administrasjon). Kravspecen beskriver en **ressursportal**:
   dokumenter, maler og veiledninger bak medlemsinnlogging — med ressursområder,
   temasider, SharePoint-dokumentvisning, globalt søk, Forms-lenker og admin-CMS.
   Faggruppe-inn/utmelding er **eksplisitt delegert til StyreWeb** (KR-08: portalen
   peker dit; «Mitt medlemskap»-knappen KR-07).
2. **Sjekklistens spørsmål 1 er dermed besvart.** «Hva er den annonserte
   selvbetjeningsportalen konkret?» → StyreWeb/Gnist er medlemsflaten (Min side,
   faggrupper, samtykker), og ressursportalen er DNDs eget bygg oppå. Det forkastede
   medlemssystem-dokumentet bekrefter arbeidsdelingen: alt «Min side»-innhold der
   (personopplysninger, faggrupper, samtykker, faktura, utmelding) skal nå dekkes av
   StyreWeb — ikke av portalen.
3. **Utredningens hovedanbefaling holder.** «Tynt lag oppå StyreWeb som master»
   (B02) er ordrett kravspecens hovedprinsipp («Ressursportalen er et medlemsområde
   og en verktøykasse. Den er ikke et nytt medlemssystem»). NFR-06 (ikke lagre
   medlemsdata utover sesjon) bekrefter samme arkitektur.
4. **To av utredningens funn er direkte gull for kravspecens vanskeligste punkter:**
   (a) medlemmer har ikke M365-konto → SharePoint-innbygging i iframe er umulig,
   dokumenter må hentes serverside via Graph API (løser KR-16–19 og kravspecens
   risiko nr. 2); (b) StyreWeb har ingen offentlig API-/SSO-dokumentasjon → kravspecens
   bærende innloggingsflyt (KR-01–03) hviler på uverifiserte antakelser, og
   sjekklistens punkt 7–9 er ferdige spørsmål til akkurat det møtet Hege foreslår.
5. **HumHub-sporet må omprioriteres.** MVP-en i kravspecen er innholdsformet
   (CMS + dokumentvisning + søk), ikke fellesskapsformet. HumHubs styrker
   (grupperom, inn/utmelding, lederadmin) ligger nå i StyreWebs domene eller i
   kravspecens fase 2. PoC-en er ikke bortkastet — den er en ferdig **fase 2-demo**
   (personalisering KR-34, samhandling, faggruppeliv). Samtidig blir v1-researchen
   (CMS-vurderingen, B08 «nedprioritert») **relevant igjen**.
6. **Vår rolle er bestillersiden, ikke byggesiden.** Team Bergen bygger; DND leverer
   avklaringer, design, innhold — og teknisk sparring. Den raskeste måten å heve
   leveransen på er å møte Team Bergen med ferdige svar på kravspecens syv «åpne
   spørsmål til utviklingsteamet» (kap. 22) og et avklart StyreWeb-bilde.

---

## 2. Hva kravspecen krever — og hvor vi står

Gruppert dekning av kravmatrisen (KR-01–35) og de ikke-funksjonelle kravene:

| Kravområde | Krav | Status i vårt arbeid | Hva som gjenstår |
|---|---|---|---|
| **Innlogging/SSO** | KR-01–06: start på DND.no, SSO mot StyreWeb, API-verifisering av aktivt medlemskap, tydelig avvisning, ingen egen konto | Arkitekturmønsteret og fallback-alternativene er ferdig utredet (B05, sjekkliste pkt. 7–9). **Uverifisert om StyreWeb kan føderere SSO i det hele tatt** | StyreWeb-møtet. Beslutningstre i kap. 5 her |
| **Mitt medlemskap / faggrupper** | KR-07–08: dyplenke til medlemskort; faggrupper håndteres i StyreWeb | Konsistent med utredningen. Krever stabile dyplenker | Bekrefte URL-struktur med StyreWeb |
| **Forside/design** | KR-09: Figma med hero, søk, ressurskort, footer | Heges skisse finnes; Figma ikke påbegynt (Yvonne) | Yvonne. Skissen + kap. 10 gir innholdet |
| **CMS/admin** | KR-10–15, 28–30: opprette/endre/skjule områder og temasider uten utvikler | **Gap i utredningen**: plattformvurderingen målte fellesskapskrav, ikke CMS-krav. Men v1-CMS-researchen dekker feltet | Teknologivalg sammen med Team Bergen (kap. 4 her) |
| **Dokumenter** | KR-16–19: SharePoint-kilde, visning i portalen, PDF/Word/Excel/PPT, nedlasting | **Løsningsmønster ferdig utredet** (serverside Graph; iframe umulig for eksterne) | Team Bergen implementerer; DND ordner SharePoint-struktur + app-tilgang |
| **Navigasjon** | KR-20–22: fast toppmeny, brødsmulesti, tilbake uten konteksttap | Rent frontend-krav; ligger i Figma/implementering | Team Bergen |
| **Søk** | KR-23–25: globalt søk i områder, temasider og dokument*titler*; søkeord/metadata | Ikke utredet spesifikt; løses av CMS-valget + metadatafelt | Forventningsstyring: MVP søker titler+metadata, ikke fulltekst i dokumentinnhold |
| **Forms/booking** | KR-26–27: Forms-lenker; booking kun som veiledningsside | Trivielt (Forms er innbyggbart også anonymt — bekreftet i v1-researchen) | — |
| **Responsivt/UU** | KR-31–32, NFR-02–03 | Standard praksis | Se WCAG-funnet i kap. 6 |
| **Fase 2** | KR-33–35: rollebasert tilgang, personalisering, AI-søk | **PoC-en demonstrerer personalisering allerede** (forslagsmotoren) | Holdes utenfor MVP — brukes som demo |
| **Drift/dokumentasjon** | NFR-05, 09–11, 13–14 + kap. 21 | Hosting/kost ferdig utredet (EU/EØS ~200 kr/mnd, Azure-tilskudd $2 000/år) | **Kravspecen lar «teknisk portalrammeverk» stå uavklart etter lansering — må eies** |

---

## 3. Hva som endres i forhold til utredningens beslutningslogg

| Beslutning | Var | Bør bli |
|---|---|---|
| **B03** (vei B vs. C) | Åpen, avgjøres av API-avklaring | **Delvis avgjort av kravspecen:** MVP er en tynn *innholds*portal (vei B-fasong) der skrivebehovet mot StyreWeb er borte (faggrupper håndteres der). API-behovet krymper til **lese medlemsstatus** + SSO |
| **B04** (HumHub) | Anbefalt for vei C | **Omposisjoneres til fase 2-kandidat** (samhandling/grupperom). Ikke riktig verktøy for MVP-ens CMS+dokument+søk-form |
| **B05** (Entra External ID) | Anbefalt innlogging | **Blir fallback**: kravspecen ønsker StyreWeb-SSO. Hvis StyreWeb ikke kan føderere, er External ID + API-statussjekk planen — brukeropplevelsen fra DND.no blir identisk |
| **B08** (CMS-sporet nedprioritert) | Vedtatt nedprioritert | **Reverseres delvis:** ressursportalen ER innholdsformet; v1-CMS-researchen (WordPress + WPO365 m.fl.) gjenbrukes i teknologivalget med Team Bergen |
| **B06** (forslagsmotor) | Vedtatt bygges selv | Uendret faglig — men flyttes til fase 2 (KR-34). PoC-en er demoen |
| **B07** (e-valg) | Anbefalt inn i kravbildet | Uendret — men **ikke** inn i denne MVP-en. Nevnes som fremtidig portalbeboer, parkeres via endringsprosessen (kap. 18.2 i kravspecen) |

---

## 4. Utkast til svar på kravspecens syv åpne spørsmål (kap. 22)

Dette er verdipapiret inn mot Team Bergen-møtet — ferdige, research-forankrede svar:

1. **Teknisk plattform?** Kravene har CMS-fasong: strukturert innhold (område → temaside
   → ressurs), redaktørflate for ikke-teknikere (NFR-05), søk, og én skreddersydd
   komponent (dokumentproxy mot SharePoint). Anbefalt utgangspunkt: et modent
   open source-CMS med god redaktøropplevelse — WordPress sto øverst i v1-researchen
   (WPO365 gir ferdig Entra/External ID-SSO); Craft CMS er verdt å vurdere for
   konsistens med dataforeningen.no (byrået Værsågod kjenner det). Full skreddersøm
   frarådes for pro bono-forvaltning (bussfaktor). **Valget tas sammen med Team Bergen
   ut fra deres kompetanse** — det viktige er kravene til formen, ikke merkenavnet.
2. **SSO mot StyreWeb?** Ukjent om StyreWeb kan opptre som identitetsleverandør
   (OIDC/SAML) — ingen offentlig dokumentasjon. Avklar i StyreWeb-møtet (kap. 5).
   Fallback som gir samme brukeropplevelse: Entra External ID (gratis < 50 000 MAU)
   som innlogging + StyreWeb-API for statussjekk ved pålogging.
3. **Hvilke StyreWeb-API-er trengs?** Minimalt: oppslag «er denne personen aktivt
   medlem?» gitt e-post/medlemsnummer, helst med webhook/endringsvarsel. Pluss
   stabile dyplenker til medlemskortet. Alt annet (profil, faggrupper, samtykker) blir
   værende i StyreWeb.
4. **SharePoint-visning?** Serverside via Microsoft Graph med applikasjonstillatelser —
   aldri iframe (SharePoint blokkerer innbygging, og medlemmene har ikke
   tenant-kontoer; verifisert i researchen). Mønster: portalen henter fila via Graph,
   viser PDF-er direkte (pdf.js), konverterer Office-formater til PDF for forhåndsvisning
   (Graph `?format=pdf`) og tilbyr original som nedlasting. Kort mellomlagring (cache)
   gir fart; navigasjonen forblir portalens egen (UX-prinsippet i kap. 8 innfris).
5. **Søk i MVP?** CMS-ets innebygde indeks over områder/temasider + metadatafelt per
   dokument (tittel, beskrivelse, søkeord — KR-25). Norsk stemming der plattformen
   støtter det. Fulltekst i dokumentinnhold og AI-søk er eksplisitt fase 2 (KR-35).
6. **Administrasjonsgrensesnitt?** CMS-ets standard redaktørflate med en låst
   innholdsmodell (ressursområde/temaside/ressurs som innholdstyper) — «configuration
   over customization» slik kravspecen selv formulerer det (kap. 17). Opplæring som
   kort video + én-siders guide (NFR-14).
7. **Drift, overvåking, dokumentasjon?** EU/EØS-hosting (~200 kr/mnd eller Azures
   nonprofit-tilskudd på $2 000/år), TLS, automatisk backup av innholdsdatabasen
   (SharePoint-dokumentene er allerede Microsofts ansvar — ansvarsdelingen NFR-11
   ber om er naturlig), gratis oppetidsovervåking (NFR-13 levert selv om den er COULD).
   **Uavklart i kravspecen og må løftes: hvem eier drift og kildekode etter lansering**
   (kap. 21 sier «avklares før lansering»). Forslag: åpen kildekode i DND-eid repo,
   driftsavtale avklart før go-live.

---

## 5. StyreWeb-møtet — agenda og beslutningstre

Hege har rett i rekkefølgen: **StyreWeb-møtet bør komme før Team Bergen-møtet**,
fordi svarene bestemmer innloggingsarkitekturen Team Bergen skal bygge.
Sjekklisten (docs/03) pkt. 7–9 er fortsatt riktig; spisset for ressursportalen:

1. **Føderering:** Kan StyreWeb opptre som identitetsleverandør (OIDC/SAML) for en
   ekstern portal? Hvilke claims følger med (medlemsnummer, status)? Testmiljø? Pris?
2. **Status-API:** Finnes endepunkt for å slå opp aktivt medlemskap (e-post/medlemsnr.)?
   Autentisering, rate limits, webhooks ved statusendring, avtaleform og pris.
3. **Dyplenker:** Stabil URL til medlemskortet («Mitt medlemskap») og til
   faggruppepåmelding i StyreWeb/Gnist? Kan lenkene ta med retur-URL tilbake til portalen?
4. **GDPR:** Databehandler-/avtalevilkår for statusoppslag fra portalen.
5. **Fallback:** Eksportformater og -frekvens hvis API mangler.

Beslutningstre (fire utfall, alle har en plan):

| | **API for medlemsstatus: JA** | **API: NEI** |
|---|---|---|
| **SSO: JA** | Kravspecens flyt bygges som spesifisert (best) | SSO autentiserer; status leses fra claims eller periodisk eksport |
| **SSO: NEI** | Entra External ID som innlogging + API-sjekk ved pålogging (samme brukeropplevelse; kravspec-tekst justeres) | External ID + eksport-synk av medlemsliste; press på Effektus eller revurder |

I tillegg besvarer møtet sjekklistens pkt. 4 (Checkin-kobling) hvis tiden tillater.

---

## 6. Kritiske funn og risikoer

1. **Innloggingsflyten er kravspecens bærebjelke og største usikkerhet.** KR-01–03
   (MUST) forutsetter StyreWeb-SSO som ingen har verifisert at finnes. Uten avklaring
   bygger Team Bergen på sand. → StyreWeb-møte først; beslutningstreet over.
2. **SharePoint-visning for brukere uten M365-konto er teknisk hovednøtt** — og
   kravspecen aner det («der dette er teknisk mulig», risiko nr. 2). Utredningen har
   allerede løsningsmønsteret (serverside Graph). Dette svaret bør Team Bergen få
   servert, ikke måtte oppdage.
3. **WCAG er trolig lovpålagt, ikke SHOULD.** Forskrift om universell utforming av
   IKT-løsninger gjelder også private virksomheter rettet mot allmennheten (minimum
   WCAG 2.0 AA). NFR-02 bør i praksis behandles som MUST — billig hvis det gjøres fra
   start, dyrt å ettermontere. Verdt en avklaring mot uu-tilsynets veileder, og et
   konkret, konstruktivt innspill til Hege.
4. **Forvaltning etter lansering er uavklart i selve kravspecen** (kap. 21: «avklares»).
   Pro bono-team forsvinner etter go-live; uten navngitt driftseier og kildekode-eierskap
   (repo, lisens) blir dette neste års krise. Foreslå: åpen kildekode i DND-eid
   organisasjon på GitHub + eksplisitt driftsavtale som del av akseptansen.
5. **Innhold er kritisk sti på DND-siden.** Portalen kan stå teknisk ferdig uten innhold
   (kravspecens egen risiko nr. 4). Startbiblioteket i kap. 10 (11 ressursområder) bør
   innholdssettes i SharePoint parallelt med utviklingen — ansvar hos Hege/administrasjonen.
6. **Søkeforventninger:** KR-24 sier dokument*titler*. Si det høyt tidlig — «søk finner
   ikke tekst inne i dokumentene i MVP» — så det aldri blir en skuffelse i akseptansetesten.

---

## 7. Levere bedre enn forventet — uten scope-vekst

Prinsippet: alt under er enten (a) allerede gjort, (b) rene kvalitetsvalg innenfor
eksisterende krav, eller (c) timer, ikke uker — og ingenting av det utvider MVP-ens
funksjonelle flate.

| # | Grep | Innsats | Hvorfor det imponerer — og ikke øker scope |
|---|---|---|---|
| 1 | **Skriftlige svar på alle syv åpne spørsmål** (kap. 4 her) levert før/i første Team Bergen-møte | Gjort | Kravspecen *ber* om svarene; ingen forventer dem ferdig utredet på dag én |
| 2 | **StyreWeb-avklaring med beslutningstre** (kap. 5 her) | Ett møte | Fjerner prosjektets største risiko; alle fire utfall har en plan |
| 3 | **Klikkbar prototype av forsiden** med de ekte ressursområdene fra kap. 10 og fungerende søk (statisk HTML) | ~En dag | Gir Yvonne, Hege og Team Bergen felles referanse lenge før Figma/kode; kastes etterpå — det er en tegning, ikke scope |
| 4 | **HumHub-PoC-en som fase 2-demo** (forslagsmotoren = KR-34 personalisering, live i dag) | Gjort | Viser retning og ambisjon uten å love noe i MVP |
| 5 | **WCAG fra start + uu-avklaring** | Kvalitetsvalg | Trolig lovpålagt uansett (kap. 6); å flagge det er profesjonelt bestillerhåndverk |
| 6 | **Personvernvennlig bruksstatistikk** (Plausible/Matomo, uten samtykkebanner) | Timer | Ikke i kravspecen — men gir Hege tall til styret om at portalen brukes. Ren driftskonfig |
| 7 | **Oppetidsovervåking** (NFR-13 er COULD) | Minutter, gratis | COULD levert som standard |
| 8 | **Automatisk lenkesjekk mot SharePoint** (ukentlig jobb som varsler om døde dokumentlenker) | Timer | Angriper ressursportalers klassiske dødsårsak: råtnende innhold. Forvaltningsstøtte, ikke funksjonalitet |
| 9 | **«Sist oppdatert» + innholdseier per temaside** | Innholdsmodellfelt | Gjør forvaltningsansvaret (kap. 21) synlig i selve portalen |
| 10 | **Tilbakemeldingslenke på hver temaside** (Forms) | Triviell | Står allerede i Heges skisse («Har du tips til innhold?») — lever den overalt |

**Scope-vakter** (like viktig som tilleggene): e-valg, forslagsmotor i MVP, rollebasert
tilgang, teknisk booking og fulltekst-/AI-søk holdes aktivt ute, med henvisning til
kravspecens egen endringsprosess (kap. 18.2). Å *bruke* bestillerens endringsprosess
er også en måte å imponere på.

---

## 8. Anbefalt rekkefølge videre

1. **Tilbakemelding til Hege** (kort): analysen her, WCAG-punktet, forvaltnings-/
   eierskapspunktet, og bekreftelse på at StyreWeb-møtet bør komme først. Avstem
   med Yvonne at skissen + kap. 10 legges til grunn for Figma.
2. **StyreWeb/Effektus-møte** med agendaen i kap. 5. Resultat: innloggingsarkitektur låst.
3. **Team Bergen-møte** med kravspecen + svarene i kap. 4 + StyreWeb-utfallet.
   Teknologivalg tas der, formet av kravenes CMS-fasong og teamets kompetanse.
4. **Parallelt (DND-siden):** startbibliotek i SharePoint (Hege), Figma (Yvonne),
   ev. klikkbar prototype (grep 3) som felles referanse.
5. **PoC-en beholdes urørt** som fase 2-demo; beslutningsloggen oppdateres
   (B03/B04/B05/B08 — se kap. 3) når punkt 2–3 er gjennomført.

---

## 9. Tillegg 12.08 (senere samme dag): portalen bygges internt

Oppdragsgivers beslutning etter at analysen over ble skrevet: **vi bygger selv og tar hele
ansvaret — Team Experis Bergen engasjeres ikke** (beslutningslogg B10). Det endrer
lesningen av analysen slik:

- **Rollebildet i kap. 1 pkt. 6 utgår.** Kravspecens «Team Bergen skal levere» (kap. 3.1)
  er nå vår backlog; «Dataforeningen leverer selv» står uendret (innhold: Hege,
  design/Figma: Yvonne). Svarene i kap. 5 er ikke lenger møteforberedelse — de er våre
  arkitekturbeslutninger.
- **Teknologivalget er vårt.** Arbeidshypotese: WordPress med egen liten
  Graph-dokumentproxy-modul — beste redaktørflate for NFR-05 (administrasjonen skal kunne
  alt uten oss), ferdig OIDC/External ID-økosystem, lavest forvaltningsrisiko i
  v1-researchen. Valideres i et pilotoppsett før forpliktelse; Craft-konsistens med
  dataforeningen.no er eneste reelle utfordrer.
- **Kritisk sti er uendret:** StyreWeb-avklaringen (kap. 6) må fortsatt tas først —
  innloggingsarkitekturen avhenger av den uansett hvem som bygger.
- **Prototypen** (`prototype/index.html`, publisert 12.08) er UX-referanse og
  innholdsstruktur for bygget — brukerreisene, søket og dokumentvisningsmønsteret derfra
  overføres til den valgte plattformen.
- **«Imponer uten scope-vekst»-listen (kap. 8) består**, men mottakeren er nå Hege/styret
  i stedet for Team Bergen. Punkt 1–3 er levert per 12.08.
- **Å flagge til Hege:** at Bergen-teamet ikke engasjeres, er en beskjed bare hun kan gi.
  Kravspecens pro bono-risiko («kapasitet varierer») erstattes av vår kapasitet — legg en
  realistisk fremdriftsplan (grovt: 40–80 timer til MVP, jf. v1-estimatene) i
  tilbakemeldingen til henne.

---

*Analyse utarbeidet med Claude Code 12.08.2026. Kilder: Kravspek.eml med vedlegg,
docs/01–04, humhub-poc/. Faktagrunnlag om SharePoint/Graph, Entra External ID-prising,
StyreWeb og CMS-vurderingen: researchrapport v2 (verifisert 11.08.2026).
Tillegg kap. 9 samme dag etter beslutning om internt bygg.*
