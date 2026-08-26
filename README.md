# Dataforeningen: Ressursportal (tidl. selvbetjeningsportal)

**Status: Byggefasen startet 26.08 — se [dnd-ressursportal](https://github.com/zberglie/dnd-ressursportal)
(lokalt: `/home/ubuntu/prosjekter/dnd-ressursportal`). Plattformvalget er tatt: WordPress
(B11); drift på OVH VPS (B12). Dette repoet er heretter utredningsarkiv; nye beslutninger
føres i bygge-repoets beslutningslogg (B11→). StyreWeb-avklaringen er fortsatt kritisk sti.**
Sist oppdatert: 26. august 2026.

## Hva dette er

Utredningsprosjekt for en selvbetjeningsportal for Den Norske Dataforenings medlemmer:
finne faggrupper, melde seg inn/ut selv, administrere egne valg, få dynamiske forslag
basert på interesser, og la fagstyrene administrere egne grupper.

Oppdraget startet 11. august 2026 («dag 1»: se på støttefunksjon for selvadministrasjon
av den frivillige delen). Samme dag ble det gjennomført research i tre spor, skrevet
rapport, bygget kjørende proof of concept og laget presentasjon. Prosjektet er nå
parkert i god stand **før beslutning** — dette dokumentet er inngangen når det tas opp
igjen.

## Innhold i prosjektet

| Sti | Hva |
|---|---|
| `README.md` | Denne filen — oversikt, status, gjenopptak |
| `docs/01-researchrapport-v2.html` | Fullstendig researchrapport med kilder (åpnes i nettleser) |
| `docs/02-presentasjon.html` | Executive-presentasjon, 12 lysbilder, distribuerbar |
| `docs/03-dag2-sjekkliste.md` | Spørsmålene som må avklares internt og med StyreWeb/Effektus |
| `docs/04-beslutningslogg.md` | Beslutninger tatt, anbefalinger gitt, og hva som står åpent |
| `docs/05-gapanalyse-ressursportal.md` | Gap-analyse (12.08) av kravspecen fra Hege (ressursportal, Team Experis Bergen) mot utredningen — se `/home/ubuntu/delt/DND portal/Kravspek.eml` |
| `docs/06-craft-arkitektur.md` | Arkitekturnotat (12.08): slik bygges portalen på Craft CMS «riktig første gang» — kontrakter rundt identitet/medlemsstatus/dokumenter, egen instans vs. hovedsiden, innholdsmodell, byggerekkefølge |
| `humhub-poc/` | Kjørende proof of concept (Docker) — se `humhub-poc/DEMO.md` for demoløype og innlogginger. Etter kravspecen: fase 2-demo |
| `prototype/index.html` | Klikkbar prototype av ressursportalen (12.08): forside etter Heges skisse, 11 ressursområder, søk, dokumentvisning, innloggingsflyt. Åpnes rett i nettleser |
| `wordpress-pilot/` | **Kjørende pilotmiljø** (Docker, port 8082): WordPress med eget tema (prototypens design), innholdsmodell som plugin, dokumentproxy-mock og kravspecens innholdsstruktur seedet. Se `wordpress-pilot/PILOT.md` |

Publiserte versjoner (private claude.ai-artifacts, deles fra sidens delemeny):
- Rapport: https://claude.ai/code/artifact/b4c40c35-5db4-44c1-bb77-0bc24d37642d
- Presentasjon: https://claude.ai/code/artifact/e694dc79-7c21-488e-92e6-7a339d9989bf
- Gap-analyse kravspec (12.08): https://claude.ai/code/artifact/82032cb1-d7b8-4ca7-b8e8-9a31469f77f2
- Klikkbar prototype (12.08): https://claude.ai/code/artifact/b8b1a82b-83eb-4c1a-9d8b-1da57fd1d257 (kopi i `/home/ubuntu/delt/DND portal/ressursportal-prototype.html`)

Distribusjonskopier ligger også i `/home/ubuntu/delt/` (presentasjon + demoguide).

## Sammendrag av funnene (detaljer og kilder i rapporten)

1. **DND er allerede i gang.** Foreningen annonserte i mars 2026 nytt medlemssystem og
   kommende selvbetjeningsportal. Innmelding kjører på **StyreWeb** (Effektus AS);
   medlemsappen **Gnist** er trolig planlagt medlemsflate. Ikke bygg parallelt —
   bygg videre.
2. **Gapet er reelt.** 38 faggruppesider uten påmeldingsknapp, ingen «Min side»,
   grupper startes per e-post. Alt oppdraget beskriver mangler i dag.
3. **Anbefalt arkitektur:** tynt lag oppå StyreWeb (som forblir master for medlemskap
   og betaling). Vei B = skreddersydd tynn portal på StyreWebs API; vei C = plattform
   (HumHub) med synk. Valget avgjøres av API-avklaringen — StyreWeb har ingen offentlig
   API-dokumentasjon.
4. **HumHub vant plattformvurderingen** (mot Open Social, WordPress+BuddyPress,
   Discourse, skreddersøm): dekker gruppekatalog, inn/utmelding, profil og
   gruppelederadministrasjon ferdig, har REST-API for registersynk og norsk i kjernen.
5. **Forslagsmotoren (interesser → gruppeforslag) finnes ikke som hyllevare** noe sted —
   liten egenutvikling (1–2 uker) uansett veivalg. PoC-en demonstrerer prinsippet.
6. **Økonomi:** ~0 kr lisenser, ~200 kr/mnd infrastruktur, medlemsinnlogging gratis
   (Entra External ID < 50 000 MAU), Azure-tilskudd $2 000/år finnes. Timer, ikke
   kroner, er kostnaden. GDPR håndterbar med EU/EØS-hosting.
7. **Vedtektskrav å ta med:** elektronisk stemmegivning ved regionale årsmøter
   («i foreningens digitale løsning», §6.4, 2025).

## Proof of concept — drift

PoC-en kjører som Docker Compose i `humhub-poc/` (HumHub 1.18.4 + MariaDB 11,
compose-prosjektnavn låst til `humhub-poc` så mappen kan flyttes trygt).

```bash
cd humhub-poc
docker compose up -d      # start (data ligger i named volumes og overlever restart)
docker compose down       # stopp
docker compose ps         # status
```

- URL: http://172.30.112.84:8080 (endre baseUrl ved behov — kommando i `humhub-poc/DEMO.md`)
- Innlogginger, demoløype og teknisk beskrivelse: `humhub-poc/DEMO.md`
- Demodata reetableres/utvides med `seed.php` (idempotent):
  `docker compose cp seed.php humhub:/seed.php && docker compose exec humhub su www-data -s /bin/bash -c 'php /seed.php'`
- Forslagsmodulen (egenutviklet, ~100 linjer): `humhub-poc/dnd-forslag/` — deployes med
  `docker compose cp dnd-forslag humhub:/data/modules-custom/dnd-forslag` (+ `php yii module/enable dnd-forslag` første gang)
- **PoC-forbehold:** HTTP uten TLS, ingen e-post konfigurert, demopassord — skal ikke
  eksponeres mot internett i denne tilstanden.

## Slik gjenopptas prosjektet

1. Les `docs/05-gapanalyse-ressursportal.md` (gjeldende kravbilde + plan), deretter
   `docs/04-beslutningslogg.md` (B09/B10 er de styrende beslutningene per 12.08).
2. **StyreWeb/Effektus-møte** med agendaen i gap-analysen kap. 6 (SSO-føderering?
   status-API? dyplenker? GDPR?). Beslutningstreet der dekker alle fire utfall.
3. **Pilotoppsett på valgt plattform** (arbeidshypotese: WordPress + egen
   Graph-dokumentproxy) med prototypen (`prototype/index.html`) som UX-referanse:
   innholdsmodell, innlogging etter StyreWeb-utfallet, dokumentvisning mot et
   test-SharePoint, søk.
4. Innhold (Hege) og Figma (Yvonne) løper parallelt; HumHub-PoC-en er utvidet 26.08 med integrasjons-PoC (DND-tema + fildeling, se humhub-poc/DEMO.md) og beholdes
   som fase 2-demo.
5. Rapport/analyse/prototype oppdateres ved å be Claude Code oppdatere
   artifact-URL-ene over (fungerer også fra en ny økt når URL-en oppgis).
