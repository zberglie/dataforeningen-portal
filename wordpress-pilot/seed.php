<?php
/**
 * Seed for pilotmiljøet. Idempotent — kjøres med:
 *   docker compose run --rm cli wp eval-file /seed.php
 * Innholdet er kravspecens kap. 10 (11 ressursområder) slik det ble
 * strukturert i den klikkbare prototypen. Alt er plassholdere.
 */

if (!defined('WP_CLI')) { exit("Kjøres via wp eval-file\n"); }

function dnd_seed_post($type, $slug, $tittel, $innhold, $rekkefolge = 0, array $meta = []) {
    $eksisterende = get_page_by_path($slug, OBJECT, $type);
    if ($eksisterende) { return $eksisterende->ID; }
    $id = wp_insert_post([
        'post_type' => $type, 'post_name' => $slug, 'post_title' => $tittel,
        'post_content' => $innhold, 'post_status' => 'publish', 'menu_order' => $rekkefolge,
        'post_excerpt' => isset($meta['_dnd_sokeord']) ? $meta['_dnd_sokeord'] : '',
    ], true);
    if (is_wp_error($id)) { WP_CLI::warning("$slug: " . $id->get_error_message()); return 0; }
    foreach ($meta as $k => $v) { update_post_meta($id, $k, $v); }
    return $id;
}

/* ---------- sider ---------- */

dnd_seed_post('page', 'mitt-medlemskap', 'Mitt medlemskap',
    '<p>Denne knappen skal ta deg til <strong>medlemskortet ditt i StyreWeb</strong> (KR-07). Der kan du oppdatere kontaktinformasjon og samtykker, se medlemsstatus og -kategori, og melde deg inn i og ut av faggrupper (KR-08).</p>'
    . '<p>I piloten er dyplenken ikke koblet — URL-strukturen avklares i StyreWeb-møtet.</p>');

dnd_seed_post('page', 'arrangementer', 'Arrangementer',
    '<p>Arrangementer og påmelding ligger på dataforeningen.no og i Checkin. Menypunktet finnes her for å vise den globale navigasjonen fra designskissen (nivå 1).</p>'
    . '<p>Skal du <em>arrangere</em> noe selv? Se ressursområdene <strong>Nettverksmøter</strong> og <strong>Slik arrangerer vi konferanser</strong> på forsiden.</p>');

dnd_seed_post('page', 'faggrupper', 'Faggrupper',
    '<p>Innmelding og utmelding i faggrupper og fagstyrer håndteres i StyreWeb — portalen peker deg til riktig sted (KR-08). Trykk <strong>Mitt medlemskap</strong> øverst.</p>'
    . '<p>Ressursene for å <em>drive</em> en faggruppe finner du i områdene «Slik driver du en faggruppe» og «Faggrupper» på forsiden.</p>'
    . '<p>I fase 2 kan portalen gi personlige gruppeforslag basert på interesser — prinsippet er demonstrert i HumHub-PoC-en (se beslutningsunderlaget).</p>');

dnd_seed_post('page', 'om-piloten', 'Om piloten',
    '<p>Dette er pilotmiljøet for Dataforeningens ressursportal, bygget internt (beslutningslogg B10) med WordPress som arbeidshypotese. Formålet er å validere plattformvalget mot kravspecen v1.2 <em>før</em> forpliktelse.</p>'
    . '<h2>Hva piloten viser</h2><ul>'
    . '<li><strong>Innholdsmodellen</strong> (KR-10–15): ressursområde → temaside → ressurs som egne innholdstyper. Nytt område = nytt innlegg; skjul = kladd; arkiver = papirkurv; rekkefølge = «Order»-feltet. Ingen kodeendring (T-04).</li>'
    . '<li><strong>Redaktørflaten</strong> (NFR-05): alt innhold redigeres i wp-admin av redaktørrollen — logg inn som <code>hege</code> og prøv.</li>'
    . '<li><strong>Søk</strong> (KR-23–25): dekker områder, temasider og dokumenttitler + søkeord-feltet. Prøv «budsjettmal» (T-07).</li>'
    . '<li><strong>Dokumentproxy-mønsteret</strong> (KR-16–19): «Last ned original» streamer via portalens proxy. I piloten genereres en plassholder-PDF; i produksjon bytter proxyen til Microsoft Graph mot SharePoint (én funksjon byttes: <code>dnd_proxy_hent_dokument()</code>).</li>'
    . '<li><strong>Innloggingsporten</strong> (KR-01–05): hele portalen krever innlogging. Lokal innlogging er plassholder for StyreWeb-SSO/Entra External ID — valget tas etter StyreWeb-møtet og påvirker ikke resten.</li>'
    . '<li><strong>Brødsmulesti, tilbakefunksjon, responsivt</strong> (KR-20–22, 31–32).</li></ul>'
    . '<h2>Ikke i piloten ennå</h2><ul>'
    . '<li>Ekte SSO/OIDC og medlemsstatus-sjekk (venter på StyreWeb-avklaring)</li>'
    . '<li>Ekte SharePoint/Graph-henting (krever app-registrering i DNDs tenant)</li>'
    . '<li>Figma-designet (Yvonne) — temaet er prototypens skisse</li>'
    . '<li>WCAG-gjennomgang, TLS/hosting, backup/overvåking (produksjonsoppsett)</li></ul>'
    . '<h2>Brukerreise-test</h2><p>Kravspecens brukerreise 7.1 på tre klikk: Forside → Administrasjon av lokallag og faggrupper → Protokoller og møtereferat → <em>Mal for protokoll</em>.</p>');

/* ---------- ressursområder / temasider / ressurser ---------- */

$data = [
  ['lokallag', 'Administrasjon av lokallag og faggrupper',
   'Protokoller, styrearbeid, vedtekter, nominasjon, møtereferat, medlemspleie og verving.', [
    ['protokoller', 'Protokoller og møtereferat', 'Maler og eksempler for protokoller og referater fra styremøter og årsmøter.', 'Administrasjonen', [
      ['mal-protokoll', 'Mal for protokoll', 'Word', 'protokoll, referat, mal'],
      ['mal-motereferat', 'Mal for møtereferat', 'Word', 'referat, møte'],
      ['eksempel-arsmoteprotokoll', 'Eksempel: årsmøteprotokoll', 'PDF', 'årsmøte, protokoll'],
    ]],
    ['styrearbeid', 'Styrearbeid', 'Styrehjul, rollebeskrivelser og hva administrasjonen hjelper til med.', 'Administrasjonen', [
      ['styrehjulet', 'Styrehjulet', 'PDF', 'styrehjul, årshjul'],
      ['rollene-i-et-styre', 'Rollene i et styre', 'PDF', 'roller, leder, styremedlem'],
      ['hva-gjor-administrasjonen', 'Hva gjør administrasjonen?', 'PDF', 'administrasjonen, støtte'],
    ]],
    ['vedtekter-nominasjon', 'Vedtekter og nominasjon', 'Gjeldende vedtekter, nominasjonsprosess og valginstruks.', 'Administrasjonen', [
      ['hovedvedtekter', 'Hovedvedtekter', 'PDF', 'vedtekter, regler'],
      ['mal-nominasjonskomite', 'Mal for nominasjonskomiteen', 'Word', 'nominasjon, valg'],
      ['valginstruks', 'Valginstruks', 'PDF', 'valg, instruks'],
    ]],
    ['medlemspleie-verving', 'Medlemspleie og verving', 'Slik tar dere godt imot nye medlemmer — og får flere av dem.', 'Administrasjonen', [
      ['tips-til-verving', 'Tips til verving', 'PDF', 'verving, rekruttering, medlemmer'],
      ['velkomstepost', 'Velkomst-e-post til nye medlemmer', 'Word', 'velkommen, e-post, mal'],
    ]],
  ]],
  ['drive-faggruppe', 'Slik driver du en faggruppe eller et styre',
   'Styretips, møterom, inkludering, møtemaler og protokoller.', [
    ['kom-i-gang-fagstyre', 'Kom i gang som fagstyre', 'Det viktigste for deg som er ny i et fagstyre — fra første møte til årshjul.', 'Faglederforum', [
      ['styretips', 'Styretips', 'PDF', 'styre, tips'],
      ['styrehjul-faggrupper', 'Styrehjul for faggrupper', 'PDF', 'styrehjul, årshjul'],
      ['motemal', 'Møtemal', 'Word', 'møte, agenda, mal'],
    ]],
    ['moter-og-moterom', 'Møter og møterom', 'Booking av møterom løses som veiledning i MVP (KR-27) — her er fremgangsmåten.', 'Administrasjonen', [
      ['booke-moterom-veiledning', 'Booke møterom — veiledning', 'PDF', 'booke, booking, møterom, rom'],
      ['sjekkliste-gode-moter', 'Sjekkliste for gode møter', 'PDF', 'sjekkliste, møte'],
    ]],
    ['inkludering-engasjement', 'Inkludering og engasjement', 'Slik senker dere terskelen for at flere deltar og bidrar.', 'Faglederforum', [
      ['tips-inkludering', 'Gode tips til inkludering', 'PDF', 'inkludering, mangfold'],
    ]],
  ]],
  ['arsmoter-landsmoter', 'Årsmøter og landsmøter',
   'Innkalling, sakslister, årshjul, valg, delegater, vedtekter og praktisk informasjon.', [
    ['gjennomfore-arsmote', 'Gjennomføre årsmøte', 'Alt du trenger for å kalle inn til og gjennomføre et ryddig årsmøte.', 'Administrasjonen', [
      ['mal-innkalling', 'Mal for innkalling', 'Word', 'innkalling, årsmøte'],
      ['mal-saksliste', 'Mal for saksliste', 'Word', 'saksliste, agenda'],
      ['sjekkliste-arsmote', 'Sjekkliste for årsmøtet', 'PDF', 'sjekkliste, årsmøte'],
      ['arshjul', 'Årshjul', 'PDF', 'årshjul, frister'],
    ]],
    ['valg-delegater', 'Valg og delegater', 'Valg av delegater og praktisk informasjon rundt regionale årsmøter.', 'Administrasjonen', [
      ['valg-av-delegater', 'Valg av delegater — slik gjør du det', 'PDF', 'delegater, valg'],
      ['praktisk-informasjon', 'Praktisk informasjon', 'PDF', 'praktisk'],
    ]],
    ['landsmotet', 'Landsmøtet', 'Frister, innkalling og styringsdokumenter for landsmøtet.', 'Administrasjonen', [
      ['landsmote-innkalling', 'Innkalling og frister', 'PDF', 'landsmøte, innkalling'],
      ['landsmote-vedtekter', 'Vedtekter', 'PDF', 'vedtekter'],
    ]],
  ]],
  ['nettverksmoter', 'Nettverksmøter',
   'Arrangørtips, SoMe-tips, deltakerrekruttering og møteromsveiledning.', [
    ['planlegge-nettverksmote', 'Planlegge nettverksmøte', 'Fra idé til gjennomført fagmøte — arrangørtipsene som gjør det enkelt.', 'Administrasjonen', [
      ['arrangortips', 'Arrangørtips', 'PDF', 'arrangere, fagmøte, tips'],
      ['sjekkliste-nettverksmoter', 'Sjekkliste for nettverksmøter', 'PDF', 'sjekkliste'],
    ]],
    ['markedsforing-deltakere', 'Markedsføring og deltakere', 'Slik får du folk til å komme — og til å komme tilbake.', 'Markedsansvarlig', [
      ['slik-far-du-deltakere', 'Slik får du deltakere', 'PDF', 'deltakere, rekruttering, påmelding'],
      ['tips-til-some', 'Tips til SoMe', 'PDF', 'some, sosiale medier, linkedin'],
    ]],
    ['rom-og-praktisk', 'Rom og praktisk', 'Veiledning for å booke rom til eventet.', 'Administrasjonen', [
      ['booke-rom-event', 'Booke rom til eventet — veiledning', 'PDF', 'booke, rom, lokale'],
    ]],
  ]],
  ['konferanser', 'Slik arrangerer vi konferanser',
   'Budsjettmal, call for papers, lokasjon, foredragsholdere, årshjul og budsjettprosess.', [
    ['okonomi-budsjett', 'Økonomi og budsjett', 'Budsjettmalen og prosessen for å spille inn til budsjettet.', 'Administrasjonen', [
      ['budsjettmal-konferanser', 'Budsjettmal for konferanser', 'Excel', 'budsjett, budsjettmal, økonomi, regneark'],
      ['budsjettprosessen', 'Budsjettprosessen', 'PDF', 'budsjett, prosess, frister'],
    ]],
    ['program-foredragsholdere', 'Program og foredragsholdere', 'Call for papers, valg av foredragsholdere og lokasjon.', 'Faglederforum', [
      ['mal-call-for-papers', 'Mal for call for papers', 'Word', 'call for papers, cfp, foredrag'],
      ['valg-av-foredragsholdere', 'Valg av foredragsholdere', 'PDF', 'foredragsholder'],
      ['valg-av-lokasjon', 'Valg av lokasjon', 'PDF', 'lokasjon, sted'],
    ]],
    ['spille-inn-ny-konferanse', 'Spille inn ny konferanse', 'Har fagstyret en konferanseidé? Slik spiller dere den inn.', 'Administrasjonen', [
      ['arshjul-dataforeningen', 'Årshjul for Dataforeningen', 'PDF', 'årshjul'],
      ['spill-inn-konferanseide', 'Spill inn ny konferanseidé', 'Forms', 'konferanse, idé, forslag'],
    ]],
  ]],
  ['landsstyret', 'Saker til landsstyret',
   'Saksmal, nye ideer, årshjul, vedtekter og informasjon om styrearbeid.', [
    ['melde-saker', 'Melde saker til landsstyret', 'Saksmalen og hvordan du følger opp saker etter behandling.', 'Landsstyret', [
      ['saksmal', 'Saksmal', 'Word', 'sak, saksmal, landsstyret'],
      ['folge-opp-saker', 'Hvordan følge opp saker', 'PDF', 'oppfølging'],
    ]],
    ['styrearbeid-landsstyret', 'Styrearbeid i landsstyret', 'Årshjul, forventninger og hva som skal til for å lykkes.', 'Landsstyret', [
      ['arshjul-landsstyret', 'Årshjul for landsstyret', 'PDF', 'årshjul'],
      ['hva-skal-til-for-a-lykkes', 'Hva skal til for å lykkes?', 'PDF', 'styrearbeid'],
      ['vedtekter-landsstyret', 'Vedtekter', 'PDF', 'vedtekter'],
    ]],
  ]],
  ['marked-kommunikasjon', 'Marked og kommunikasjon',
   'Fagblogg, pressemeldinger, markedsplan, LinkedIn, brandmanual, PPT-mal og brevmal.', [
    ['profil-og-maler', 'Profil og maler', 'Brandmanualen og malene som holder Dataforeningen visuelt samlet.', 'Markedsansvarlig', [
      ['brandmanual', 'Brandmanual', 'PDF', 'brand, profil, logo, farger'],
      ['presentasjonsmal', 'Presentasjonsmal', 'PowerPoint', 'ppt, presentasjon, mal'],
      ['brevmal', 'Brevmal', 'Word', 'brev, mal'],
      ['maler-some-poster', 'Maler for poster til SoMe', 'Lenke', 'some, poster'],
    ]],
    ['skrive-og-publisere', 'Skrive og publisere', 'Fagblogg, pressetips og retningslinjer for å skrive på vegne av foreningen.', 'Markedsansvarlig', [
      ['fagblogg-slik-skriver-du', 'Fagblogg — slik skriver du blogg', 'PDF', 'blogg, skrive'],
      ['retningslinjer-skrive', 'Retningslinjer for å skrive på vegne av Dataforeningen', 'PDF', 'retningslinjer'],
      ['pressetips', 'Pressetips', 'PDF', 'presse, media'],
    ]],
    ['markedsplan', 'Markedsplan', 'Malen for markedsplan og tips til LinkedIn.', 'Markedsansvarlig', [
      ['mal-markedsplan', 'Mal for markedsplan', 'Word', 'markedsplan'],
      ['tips-linkedin', 'Tips til LinkedIn', 'PDF', 'linkedin, some'],
    ]],
  ]],
  ['faggrupper-ressurser', 'Faggrupper',
   'Starte faggruppe, rekruttere, fagstyrets rolle og partnerpitch.', [
    ['starte-faggruppe', 'Starte en faggruppe', 'Fra idé til etablert faggruppe — slik går dere frem.', 'Administrasjonen', [
      ['slik-starter-du-faggruppe', 'Slik starter du en faggruppe', 'PDF', 'starte, ny gruppe, etablere'],
      ['fagstyrets-rolle', 'Fagstyrets rolle', 'PDF', 'fagstyre, rolle'],
    ]],
    ['rekruttere-medlemmer', 'Rekruttere medlemmer', 'Rekrutteringstips og pitch for partnere.', 'Faglederforum', [
      ['slik-rekrutterer-du', 'Slik rekrutterer du', 'PDF', 'rekruttere, verving'],
      ['partnerpitch', 'Partnerpitch', 'PowerPoint', 'partner, pitch, sponsor'],
    ]],
  ]],
  ['tips-til-foredrag', 'Tips til foredrag',
   'Gode foredrag, innholdsstruktur, verktøy og publikumsengasjement.', [
    ['lage-godt-foredrag', 'Lage et godt foredrag', 'Struktur, innhold og forberedelser som løfter foredraget.', 'Faglederforum', [
      ['hvordan-lage-gode-foredrag', 'Hvordan lage gode foredrag', 'PDF', 'foredrag, presentasjon'],
      ['tips-innhold-struktur', 'Tips til innhold og struktur', 'PDF', 'struktur, innhold'],
    ]],
    ['verktoy-publikum', 'Verktøy og publikum', 'Hjelpemidler og teknikker for å engasjere publikum.', 'Faglederforum', [
      ['verktoy-hjelpemidler', 'Verktøy og hjelpemidler', 'PDF', 'verktøy'],
      ['engasjere-publikum', 'Hvordan engasjere publikum', 'PDF', 'publikum, engasjement'],
    ]],
  ]],
  ['ildsjelmanual', 'Ildsjelmanual',
   'Hva en ildsjel er, engasjement, nettverk og gjennomføring.', [
    ['ildsjelmanualen', 'Ildsjelmanualen', 'Manualen for deg som vil drive aktiviteter fremover i Dataforeningen.', 'Administrasjonen', [
      ['ildsjelmanualen-dok', 'Ildsjelmanualen', 'PDF', 'ildsjel, manual, frivillig'],
      ['hva-er-en-ildsjel', 'Hva er en ildsjel i Dataforeningen?', 'PDF', 'ildsjel'],
    ]],
    ['engasjement-nettverk', 'Engasjement og nettverk', 'Slik skaper du engasjement og bygger nettverk rundt aktivitetene dine.', 'Faglederforum', [
      ['skape-engasjement', 'Hvordan skape engasjement', 'PDF', 'engasjement'],
      ['bygge-nettverk', 'Bygge nettverk', 'PDF', 'nettverk'],
    ]],
  ]],
  ['bidra-tilbake', 'Bidra tilbake',
   'Forslag til forbedringer, nye ressurser, foredrag og frivillig arbeid — via skjema.', [
    ['meld-deg-som-frivillig', 'Meld deg som frivillig', 'Vil du bidra i en faggruppe, på et arrangement eller i regionen? Meld interesse her.', 'Administrasjonen', [
      ['frivilligregistrering', 'Frivilligregistrering', 'Forms', 'frivillig, bidra, melde interesse'],
    ]],
    ['foresla-forbedringer', 'Foreslå forbedringer og nye ressurser', 'Har du tips til innhold eller savner noe? Sammen gjør vi ressursportalen enda bedre.', 'Administrasjonen', [
      ['tips-til-innhold', 'Har du tips til innhold eller savner noe?', 'Forms', 'tips, forslag, forbedring, tilbakemelding'],
    ]],
    ['bli-foredragsholder', 'Bli foredragsholder', 'Meld interesse for å holde foredrag i en faggruppe eller på en konferanse.', 'Faglederforum', [
      ['meld-interesse-foredragsholder', 'Meld interesse som foredragsholder', 'Forms', 'foredrag, foredragsholder'],
    ]],
  ]],
];

$antall = ['omrade' => 0, 'temaside' => 0, 'ressurs' => 0];
foreach ($data as $oi => $o) {
    list($oslug, $otittel, $oblurb, $temaer) = $o;
    $oid = dnd_seed_post('omrade', $oslug, $otittel, '<p>' . $oblurb . '</p>', $oi + 1);
    if (!$oid) { continue; }
    $antall['omrade']++;
    foreach ($temaer as $ti => $t) {
        list($tslug, $ttittel, $tintro, $teier, $ressurser) = $t;
        $tid = dnd_seed_post('temaside', $tslug, $ttittel, '<p>' . $tintro . '</p>', $ti + 1, [
            '_dnd_omrade' => $oid, '_dnd_eier' => $teier,
        ]);
        if (!$tid) { continue; }
        $antall['temaside']++;
        foreach ($ressurser as $ri => $r) {
            list($rslug, $rtittel, $rtype, $rsok) = $r;
            $rid = dnd_seed_post('ressurs', $rslug, $rtittel,
                '<p>Plassholder — i produksjon peker denne til dokumentet i SharePoint.</p>', $ri + 1, [
                '_dnd_temaside' => $tid, '_dnd_type' => $rtype, '_dnd_sokeord' => $rsok,
                '_dnd_url' => 'https://dataforeningen.sharepoint.example/' . $rslug,
            ]);
            if ($rid) { $antall['ressurs']++; }
        }
    }
}

/* ---------- brukere ---------- */

if (!username_exists('hege')) {
    wp_insert_user(['user_login' => 'hege', 'user_pass' => 'DndPilot2026!', 'role' => 'editor',
        'display_name' => 'Hege (redaktør)', 'user_email' => 'hege@example.invalid']);
}
if (!username_exists('kari')) {
    wp_insert_user(['user_login' => 'kari', 'user_pass' => 'DndPilot2026!', 'role' => 'subscriber',
        'display_name' => 'Kari Nordmann', 'user_email' => 'kari@example.invalid']);
}

/* ---------- innstillinger ---------- */

update_option('blogname', 'Dataforeningen Ressursportal');
update_option('blogdescription', 'Din komplette verktøykasse for faggrupper, styrer og ildsjeler');
update_option('timezone_string', 'Europe/Oslo');
update_option('date_format', 'j. F Y');

WP_CLI::success(sprintf('Seed ferdig: %d områder, %d temasider, %d ressurser (+ 4 sider, 2 brukere).',
    $antall['omrade'], $antall['temaside'], $antall['ressurs']));
