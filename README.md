# Dataforeningen: Selvbetjeningsportal for den frivillige delen

**Status: Utredningsfase fullført — klar for beslutningsfase.**
Sist oppdatert: 11. august 2026.

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
| `humhub-poc/` | Kjørende proof of concept (Docker) — se `humhub-poc/DEMO.md` for demoløype og innlogginger |

Publiserte versjoner (private claude.ai-artifacts, deles fra sidens delemeny):
- Rapport: https://claude.ai/code/artifact/b4c40c35-5db4-44c1-bb77-0bc24d37642d
- Presentasjon: https://claude.ai/code/artifact/e694dc79-7c21-488e-92e6-7a339d9989bf

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

1. Les `docs/04-beslutningslogg.md` (hva som er anbefalt og hva som står åpent),
   deretter `docs/03-dag2-sjekkliste.md` (hva som må avklares først).
2. Gjennomfør avklaringene med sekretariatet og Effektus/StyreWeb.
3. Med svarene på plass: velg vei B eller C (kriterier i beslutningsloggen) og
   utvid PoC-en til pilot med 3–5 ekte faggrupper, ekte innlogging
   (Entra External ID via OIDC) og enkel synk fra registeret.
4. Rapport og presentasjon oppdateres enklest ved å be Claude Code oppdatere
   artifact-URL-ene over (fungerer også fra en ny økt når URL-en oppgis).

## Naturlige neste byggekloss i PoC-en (uavhengig av avklaringene)

- OIDC-innlogging mot en Entra External ID-testtenant (erstatter lokale passord)
- Mock av StyreWeb-synk mot HumHubs REST-API (demonstrerer K6)
- Interessevelger i onboarding (i dag: fritekstfelt på profilen)
- TLS + e-post hvis demoen skal vises utenfor serveren
