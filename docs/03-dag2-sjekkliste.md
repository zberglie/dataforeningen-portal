# Dag 2-sjekkliste: avklaringer før beslutning

Spørsmålene som må besvares før veivalget (B: tynn portal på API / C: plattform med
synk) kan tas. Rekkefølgen er prioritert. Kilder og bakgrunn: researchrapporten v2.

## Internt (sekretariatet / administrasjonen)

1. **Hva er den annonserte «selvbetjeningsportalen» konkret?**
   (Nyhetsbrev mars 2026 lovet nytt medlemssystem + portal.) Er det StyreWebs
   portal/Gnist-utrulling, eller et planlagt eget bygg? Hvem eier prosjektet internt,
   og hva er status/tidslinje? → *Avgjør om dette prosjektet former det pågående
   eller foreslår laget oppå.*
2. **Hvordan er faggruppene og regionene modellert i StyreWeb i dag** —
   organisasjonsledd, grupper eller aktiviteter? Kan ett medlem ha mange
   gruppemedlemskap med selvbetjent inn/ut?
3. **Hva var forrige medlemssystem**, og overlevde historiske deltakelses-/
   interessedata migreringen? (Treningsgrunnlag for forslagsmotoren.)
4. **Er Checkin koblet til medlemsregisteret** (medlemspris-validering ved påmelding,
   deltakelse tilbake til registeret) — eller kjører de parallelt?
5. **Hvilke grupper lever på Meetup**, og skal portalen erstatte eller peke til dem?
   Finnes det per-gruppe-e-postlister utenom Mailchimp?
6. **Eierskap:** hvem skal forvalte portalen over tid (administrasjon, frivillige,
   innleid)? → *Vedlikeholdstimer er den reelle kostnaden; svaret påvirker veivalget.*

## Mot Effektus / StyreWeb

7. **Finnes StyreWeb-API-et for kunder?** (Det finnes spor av en API-innlogging, men
   ingen offentlig dokumentasjon.) Omfang: lese medlemmer/medlemsstatus? Skrive
   gruppemedlemskap? Webhooks? Autentisering, rate limits, og **pris/vilkår for
   API-avtale**.
8. **Kan StyreWeb/Gnist føderere innlogging** (OIDC/SAML)? Er Vipps Login
   tilgjengelig utover innmeldingsflyten? Hvis nei: hvor god er e-post-/mobilkvaliteten
   i registeret for kobling mot ekstern innlogging (Entra External ID)?
9. **Dataeksport:** formater og frekvens for bulk-uttrekk (fallback for synk), og
   databehandlervilkår for en portal som leser medlemsdata.

## Beslutningskriterium (fra rapporten)

- API finnes med les/skriv på medlemskap → **vei B** (tynn portal; HumHub kan likevel
  brukes som ramme for grupperommene).
- API mangler eller er bare les/eksport → **vei C** (HumHub med eksport-synk og
  dyplenker til StyreWeb-skjemaer for det som må skrives) — eller forhandle API-et frem.
- Uansett vei: forslagsmotoren er egenutvikling (1–2 uker), og StyreWeb forblir master.
