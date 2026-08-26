# Beslutningslogg

Formatet er lett ADR-inspirert: hva, status, begrunnelse, konsekvens.
Statuser: **Anbefalt** (utredningens råd, ikke vedtatt), **Vedtatt**, **Åpen**.

> **26.08.2026: Loggen er avsluttet her.** Byggefasen startet; B11 og videre føres i
> [dnd-ressursportal/docs/04-beslutningslogg.md](https://github.com/zberglie/dnd-ressursportal/blob/main/docs/04-beslutningslogg.md).
> B10-hypotesen (WordPress) er vedtatt som B11 der.

---

## B01 · Målgruppe: medlemmene, ikke primært ansatte
**Status: Vedtatt (oppdragsgivers presisering, 11.08.2026)**
Oppdraget gjelder selvadministrasjon av den frivillige delen; medlemmer uten konto i
M365-tenanten er hovedbrukere. Konsekvens: vanlig Entra ID-SSO og SharePoint-innbygging
er utilstrekkelig; medlemsinnlogging må løses med Entra External ID, Vipps eller
register-koblede kontoer, og M365-innhold hentes serverside.

## B02 · Bygge videre på StyreWeb som master
**Status: Anbefalt**
StyreWeb er (med høy sannsynlighet) foreningens nye medlemssystem, og DND har selv
annonsert en selvbetjeningsportal. Portalen bygges som tynt lag; medlemskap og
betaling forblir i StyreWeb. Alternativet (egen medlemsdatabase i portalen) gir
synkdrift, dobbelt GDPR-ansvar og to sannheter — frarådet.
Konsekvens: API-/eksportavklaring med Effektus er kritisk sti (sjekkliste pkt. 7–9).

## B03 · Veivalg B (tynn portal på API) vs. C (plattform med synk)
**Status: Åpen — avgjøres av dag 2-avklaringene**
Kriterium: se `03-dag2-sjekkliste.md`. Begge veier beholder StyreWeb som master og
krever samme lille egenutviklede forslagsmotor.

## B04 · HumHub som plattform for PoC (og kandidat for vei C)
**Status: Vedtatt for PoC; Anbefalt for vei C**
Vant vurderingen mot Open Social (Drupal-drift for tung), WordPress + BuddyPress
(bokmål 76 %, fallende økosystem), Discourse (forum, feil fasong) og full skreddersøm
(varig utvikleravhengighet). Dekker K1/K2/K3/K5 native, REST-API for synk, norsk i
kjernen, lav driftsbyrde. Kjente hull: forslagsmotor må egenutvikles, OIDC settes opp
manuelt, REST-modulen er beta, AGPL → publiser egne moduler som åpen kildekode.

## B05 · Identitet: Entra External ID (evt. supplert med Vipps Login)
**Status: Anbefalt**
Gratis under 50 000 MAU (DND: 5 000–8 500 medlemmer), standard OIDC, egen katalog
adskilt fra tenanten. Vipps brukes allerede i StyreWeb-innmeldingen og kan vurderes
som supplement. Åpent: kobling identitet ↔ registeroppføring (e-post-/mobilkvalitet,
sjekkliste pkt. 8).

## B06 · Interessematching bygges selv
**Status: Vedtatt (verifisert i research + demonstrert i PoC)**
Ingen vurdert plattform eller norsk medlemssystem har interesse→gruppe-forslag som
hyllevare. PoC-modulen `dnd-forslag` viser prinsippet (tag-matching med ordgrenser,
ekskludering av eksisterende medlemskap, topp 4). Full versjon: tagger fra registeret,
onboarding-interessevelger, evt. vekting på region. Estimat 1–2 uker.

## B07 · E-valg tas inn i kravbildet
**Status: Anbefalt**
Vedtektene (§6.4, 2025) krever elektronisk stemmegivning ved regionale årsmøter
«i foreningens digitale løsning». Portalen med verifisert medlemsidentitet er naturlig
plassering. Åpent: egen modul vs. tredjepartsløsning; kravspesifikasjon ikke påbegynt.

## B08 · Publiserings-CMS er et separat spor
**Status: Vedtatt (nedprioritert)**
V1-utredningen (WordPress + WPO365 m.m.) gjelder et eventuelt publiserings-intranett.
dataforeningen.no kjører Craft CMS; portalbehovet er ikke et CMS-behov. Tas kun opp
igjen hvis publiseringsflater etterspørres.

## B09 · Ressursportal-kravspecen (v1.2) er gjeldende scope
**Status: Vedtatt (kravspec mottatt fra Hege Svendsen 12.08.2026)**
Kravspecen definerer produktet som en **ressursportal** (dokumenter/maler/veiledninger bak
medlemsinnlogging, StyreWeb-SSO + API-verifisering, SharePoint-visning, søk, admin-CMS).
Faggruppe-inn/utmelding delegeres til StyreWeb (KR-08). Full gap-analyse: `05-gapanalyse-ressursportal.md`.
Konsekvens: B03 delvis avgjort (tynn innholdsportal, kun lese-API-behov), B04 omposisjonert
(HumHub = fase 2-kandidat; PoC-en beholdes som fase 2-demo), B05 blir fallback (kravspecen
ønsker StyreWeb-SSO), B08 delvis reversert (CMS-researchen fra v1 er relevant igjen).

## B10 · Portalen bygges internt — ikke av Team Experis Bergen
**Status: Vedtatt (oppdragsgivers beslutning, 12.08.2026)**
Vi tar hele ansvaret selv: teknologivalg, utvikling, drift og forvaltning. Kravspecens
ansvarsdeling (kap. 3, «Team Bergen skal levere») blir vår backlog; «åpne spørsmål til
utviklingsteamet» (kap. 22) besvares av oss (utkast i gap-analysen kap. 5).
Konsekvenser: (1) Hege må avklare med Bergen-teamet at de ikke engasjeres — utenfor vår
kontroll, flagges til henne; (2) StyreWeb-avklaringen er fortsatt kritisk sti og første steg;
(3) teknologivalget tas av oss — arbeidshypotese: **WordPress + egen Graph-dokumentproxy**
(beste admin-flate for NFR-05, ferdig SSO-økosystem, lavest forvaltningsrisiko iht.
v1-researchen), valideres i pilotoppsett før forpliktelse; (4) klikkbar prototype
(`prototype/index.html`, 12.08) er UX-referansen for bygget.

---

## Forkastede alternativer (kortversjon — detaljer i rapporten)

- **Directus** (ikke lenger open source), **Strapi** (admin-SSO ~$195/mnd),
  **Payload** (varig utvikleravhengighet), **Drupal** (Entra-SSO umoden på v11),
  **Joomla** (økonomisk risiko i stiftelsen), **Ghost** (ingen SSO, feil kategori),
  **Grav** (økosystem i overgang), **headless + Next.js** generelt (Graph Toolkit
  pensjonert aug. 2026, bussfaktor).
- **Hivebrite** (nærmest funksjonelt, enterprise-pris), **Wild Apricot/Glue Up**
  (svak norsk betalings-/medlemsflyt), **Dynamics nonprofit-CRM** (produktet
  pensjoneres des. 2026; partner-bygg).
