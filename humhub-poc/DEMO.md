# Demo: Selvbetjeningsportal for Dataforeningen (PoC)

**URL:** http://minibedrift.mshome.net:8080
(mshome-navnet overlever VM-omstart; bytt baseUrl ved behov — se «Drift» nederst.)

HumHub 1.18.4 (open source, AGPL) satt opp som proof of concept for medlemmenes
selvadministrasjon av faggrupper, jf. researchrapporten v2.

## Innlogginger

| Bruker | Passord | Rolle i demoen |
|---|---|---|
| `admin` | `DndPoc2026!` | Administrator, medlem/eier av alle grupper |
| `kari` | `DndDemo2026!` | Medlem av AI-gruppen; interesser: KI, maskinlæring, dataanalyse |
| `ola.hansen` | `DndDemo2026!` | Har søkt om medlemskap i Informasjonssikkerhet; interesser: sikkerhet, personvern, sky |
| `ingrid` | `DndDemo2026!` | Medlem av SWT og Trondheim Agile; interesser: testing, smidig, produktledelse |

## Demoløype (ca. 10 min)

1. **Logg inn som `kari`.** På oversikten (dashbordet) vises panelet **«Forslag til deg»**
   i høyrekolonnen: BI & Analytics foreslås fordi interessen «dataanalyse» matcher gruppen.
   Dette panelet er PoC-ens egenutviklede modul — selve «kartlegg ønsker → dynamiske forslag»-ideen.
2. **Vis gruppeoppdagelse:** Meny → «Spaces»-katalogen viser alle faggruppene med
   region i navnet. Klikk deg inn i «BI & Analytics» og trykk **Bli medlem** — selvbetjent
   innmelding uten å gå via sekretariatet. (Forslaget forsvinner fra dashbordet etterpå —
   du er jo medlem nå.)
3. **Endre interesser:** Profil → Rediger → feltet **Interesser** (f.eks. legg til «helse»).
   Tilbake på dashbordet dukker eHelse (Midt-Nord) opp som nytt forslag.
4. **Logg inn som `ola.hansen`:** Forslagene hans er andre enn Karis (sikkerhet/sky).
   Merk «Søknad»-etiketten på IT-sikkerhet (Vest) — søknadsbasert gruppe.
5. **Logg inn som `admin`:** Gå til gruppen **Informasjonssikkerhet (Sør-Øst)** →
   medlemmer → ventende forespørsler: Olas søknad ligger i **godkjenningskøen**.
   Godkjenn eller avslå — dette er fagstyrets selvbetjente lederadministrasjon.
6. **Vis det lukkede rommet:** «Fagstyret AI (internt)» er usynlig for ikke-medlemmer
   (logg inn som ingrid og se at gruppen ikke finnes i katalogen) — demo av
   invitasjonsbaserte styrerom.

## Hva demoen viser (koblet til kravene i rapporten)

| Krav | Demonstrert av |
|---|---|
| K1 Gruppeoppdagelse | Space-katalogen med alle faggruppene |
| K2 Selvbetjent inn/utmelding | «Bli medlem»-knappen (åpne grupper) og søknadsflyt (Informasjonssikkerhet) |
| K3 Egen profil og valg | Profilfeltet «Interesser», varslingsinnstillinger |
| K4 Interesser → dynamiske forslag | Egenutviklet modul `dnd-forslag` (panelet «Forslag til deg») |
| K5 Gruppelederadministrasjon | Godkjenningskø + per-gruppe-roller (eier/admin/moderator) |
| K6 Integrasjon | Ikke i PoC — neste steg er OIDC-innlogging (Entra External ID) og synk mot StyreWeb via REST-API-et |

## Teknisk

- Alt ligger i `/home/ubuntu/prosjekter/dataforeningen-portal/humhub-poc/`:
  `docker-compose.yml` (HumHub 1.18.4 + MariaDB 11), `seed.php` (demodata, idempotent),
  `dnd-forslag/` (forslagsmodulen, ~100 linjer PHP).
- Forslagslogikken: brukerens kommaseparerte interesser matches mot gruppenavn +
  beskrivelse med ordgrense-regex; grupper brukeren alt er medlem av (eller har søkt på)
  filtreres bort; topp 4 vises. I en full løsning byttes dette til tagger fra
  StyreWeb/medlemsregisteret.

## Drift

```bash
cd /home/ubuntu/prosjekter/dataforeningen-portal/humhub-poc
docker compose up -d          # start
docker compose down           # stopp (data beholdes i volumer)
docker compose down -v        # slett alt inkl. data
```

Bytte adresse (hvis demoen skal vises fra annen maskin/nett):
```bash
docker compose exec humhub su www-data -s /bin/bash -c \
  'cd /opt/humhub/protected && php yii settings/set base baseUrl "http://NY-ADRESSE:8080"'
```

PoC-forbehold: kjører på HTTP uten TLS, e-postutsending er ikke konfigurert, og
passordene over er demopassord. Ikke eksponer mot internett i denne tilstanden.

## Integrasjons-PoC med ressursportalen (26.08.2026)

Demonstrerer «én side, to motorer» + dokumentdeling i gruppene:

1. **Felles drakt:** eget barnetema `DND` (`dnd-tema/DND/scss/variables.scss`) med
   portalens merkevaretokens (lime #E5FF54, ink #1F1F1F, blå #3E7FFF, flater, radius,
   fontstack). Arver alt fra kjernetemaet — kun variabler, ingen maloverstyringer
   (bevisst: oppgraderingstrygt). Aktivt via `php yii theme/switch DND`.
2. **Dokumentdeling per gruppe:** filmodulen `cfiles` (offisiell, gratis) installert og
   aktivert i alle 9 spaces (`aktiver-filmodul.php`, idempotent). Gruppemedlemmer får
   fanen **Filer** i spacet: mapper, opplasting, nedlasting — tilgang følger
   space-medlemskap automatisk.
3. **Kryssnavigasjon:** portalens Faggrupper-side lenker hit (lagt inn i dev-basen,
   ikke i seed — PoC-markert).

**Demoløype fildeling:** logg inn som `kari` → space «BI & Analytics» → fanen «Filer»
→ last opp et dokument → logg inn som `ingrid` (ikke medlem) → hun ser ikke filen.
Medlemskap = tilgang, uten ekstra administrasjon.

**Reetablering** (etter `docker compose down -v`):
```bash
docker compose cp dnd-tema/DND humhub:/data/themes/DND
docker compose exec humhub sh -c 'chown -R www-data:www-data /data/themes/DND'
docker compose exec humhub su www-data -s /bin/sh -c 'php /opt/humhub/protected/yii theme/switch DND'
docker compose exec humhub su www-data -s /bin/sh -c 'php /opt/humhub/protected/yii module/install cfiles && php /opt/humhub/protected/yii module/enable cfiles'
docker compose cp aktiver-filmodul.php humhub:/aktiver-filmodul.php
docker compose exec humhub su www-data -s /bin/sh -c 'php /aktiver-filmodul.php'
```
Tilbake til standardtema: `php yii theme/switch HumHub`.

**Det PoC-en bevisst ikke viser** (venter på StyreWeb-avklaringen): felles
OIDC-innlogging (én pålogging på tvers) og medlemskapssynk StyreWeb → spaces.
Arkitekturen for begge er beskrevet i samtaleloggen/fase 2-underlaget.

## Felles innlogging med portalen (27.08.2026)

HumHub er nå OIDC-klient av dev-identitetsleverandøren (Keycloak, port 8084 — se
`dnd-ressursportal/sso/` og `docs/06-innlogging-sso.md` der). Knappen
**«Dataforeningen-ID»** på innloggingssiden logger inn via Keycloak; er du allerede
innlogget i portalen, kommer du rett inn uten passord (SSO). Kontoer lenkes på e-post
(kari@dnd-poc.local osv.); demobrukernes SSO-passord er `DndSso2026!`.

Teknisk: offisiell modul `auth-keycloak` (krever BCMath → `Dockerfile`-overlegget),
konfigurert med `php yii settings/set auth-keycloak …` (enabled 1, clientId humhub,
clientSecret humhub-sso-poc-2026, realm dataforeningen,
baseUrl http://minibedrift.mshome.net:8084, usernameMapper preferred_username,
title Dataforeningen-ID). `extra_hosts: host-gateway` i compose lar containeren nå
IdP-en på nettleserens adresse. Lokale passord virker fortsatt (PoC).

## REST-API + faggruppefane i portalen (27.08.2026, B16)

- **REST-modulen** er installert og aktivert (`module/install rest`), basic auth på
  (`settings/set rest enableBasicAuth 1`, `enabledForAllUsers 1`). Portalens plugin
  `dnd-faggrupper` bruker API-et serverside: Følg/Meld av i portalens faggruppefane
  virker umiddelbart mot spacene her.
- **To PoC-feller løst for skrivehandlinger:** (1) e-post uten SMTP ga heng →
  `useFileTransport` i `/data/config/common.php` (deployes med
  `docker compose cp` av oppdatert fil ved reetablering); (2) Mercure-push ga
  60 s heng + 500 ved medlemsfjerning → slått av med
  `HUMHUB_DOCKER__MERCURE_ENABLE: "false"` i compose (live-oppdateringer = polling).
