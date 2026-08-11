# Demo: Selvbetjeningsportal for Dataforeningen (PoC)

**URL:** http://172.30.112.84:8080
(Fungerer fra nettverket serveren står i. Bytt adresse ved behov — se «Drift» nederst.)

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
