<?php
/**
 * Seed-skript for Dataforeningen-PoC. Kjøres i containeren:
 *   php /seed.php
 * Bootstrapper HumHub-konsollappen og oppretter demobrukere,
 * faggruppe-spaces, interessefelt og litt innhold. Idempotent.
 */

use humhub\services\BootstrapService;

$protectedPath = '/opt/humhub/protected';
chdir($protectedPath);
$loader = require($protectedPath . '/vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createMutable($protectedPath . '/../', '.env');
$dotenv->safeLoad();
$loader->addClassMap(['humhub\\services\\BootstrapService' => $protectedPath . '/humhub/services/BootstrapService.php']);

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');
require($protectedPath . '/vendor/yiisoft/yii2/Yii.php');
Yii::setAlias('@humhub', $protectedPath . '/humhub');
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));
defined('STDOUT') or define('STDOUT', fopen('php://stdout', 'w'));

$bootstrap = new BootstrapService();
$config = $bootstrap->getConfig('console');
new humhub\components\console\Application($config); // setter Yii::$app

use humhub\modules\user\models\User;
use humhub\modules\user\models\Profile;
use humhub\modules\user\models\Password;
use humhub\modules\user\models\ProfileField;
use humhub\modules\user\models\ProfileFieldCategory;
use humhub\modules\space\models\Space;
use humhub\modules\space\models\Membership;
use humhub\modules\post\models\Post;

$out = fn($m) => print("$m\n");

/* 0. Standarddata som webinstalleren normalt legger inn (profilkategorier, grupper m.m.) */
humhub\modules\installer\libs\InitialData::bootstrap();
$out('InitialData::bootstrap kjørt');

$admin = User::findOne(['username' => 'admin']);

/* 1. Profilfelt "Interesser" */
$cat = ProfileFieldCategory::find()->orderBy(['sort_order' => SORT_ASC])->one();
$field = ProfileField::findOne(['internal_name' => 'interesser']);
if (!$field) {
    $field = new ProfileField();
    $field->internal_name = 'interesser';
    $field->title = 'Interesser';
    $field->description = 'Kommaseparerte fagområder du interesserer deg for';
    $field->field_type_class = humhub\modules\user\models\fieldtype\Text::class;
    $field->profile_field_category_id = $cat->id;
    $field->sort_order = 900;
    $field->editable = 1;
    $field->visible = 1;
    $field->show_at_registration = 1;
    if ($field->save()) { $out('Profilfelt "Interesser" opprettet'); }
    else { $out('FEIL profilfelt: ' . print_r($field->getErrors(), true)); }
}
/* Sørg for at profilkolonnen faktisk finnes (admin-UI kaller fieldType->save()) */
if (!Profile::columnExists('interesser')) {
    $field->fieldType->save();
    Yii::$app->getDb()->getSchema()->refreshTableSchema(Profile::tableName());
    $out('Profilkolonne "interesser" lagt til: ' . var_export(Profile::columnExists('interesser'), true));
}

/* 2. Demobrukere */
$users = [
    ['kari',   'kari@dnd-poc.local',   'Kari',   'Nordmann', 'KI, maskinlæring, dataanalyse'],
    ['ola.hansen', 'ola@dnd-poc.local', 'Ola', 'Hansen', 'sikkerhet, personvern, sky'],
    ['ingrid', 'ingrid@dnd-poc.local', 'Ingrid', 'Berg',     'testing, smidig, produktledelse'],
];
$userModels = [];
foreach ($users as [$uname, $email, $first, $last, $interesser]) {
    $u = User::findOne(['username' => $uname]);
    if (!$u) {
        $u = new User();
        $u->scenario = User::SCENARIO_EDIT_ADMIN;
        $u->load(['username' => $uname, 'email' => $email], '');
        $u->status = User::STATUS_ENABLED;
        if (!$u->save()) { $out("FEIL bruker $uname: " . print_r($u->getErrors(), true)); continue; }
        $p = new Profile();
        $p->scenario = Profile::SCENARIO_EDIT_ADMIN;
        $p->user_id = $u->id;
        $p->load(['firstname' => $first, 'lastname' => $last], '');
        $p->save();
        $pw = new Password(['user_id' => $u->id]);
        $pw->setPassword('DndDemo2026!');
        $pw->save(false);
        $out("Bruker $uname opprettet");
    }
    $prof = Profile::findOne(['user_id' => $u->id]);
    if ($prof) { $prof->interesser = $interesser; $prof->save(false); }
    $userModels[$uname] = $u;
}
$adminProf = Profile::findOne(['user_id' => $admin->id]);
if ($adminProf) { $adminProf->interesser = 'KI, sky, arkitektur'; $adminProf->save(false); }

/* 3. Faggrupper som spaces */
$spaces = [
    ['AI – Kunstig intelligens (Sør-Øst)', 'KI, maskinlæring, generativ AI og språkmodeller i praksis. Region Sør-Øst.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#7C4DBE'],
    ['BI & Analytics (Sør-Øst)', 'Business intelligence, dataanalyse, datavarehus og visualisering. Arrangerer Make Data Smart.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#2E86AB'],
    ['Informasjonssikkerhet (Sør-Øst)', 'Informasjonssikkerhet, cybersikkerhet, personvern, GDPR og risiko. Søknadsbasert.', Space::JOIN_POLICY_APPLICATION, Space::VISIBILITY_REGISTERED_ONLY, '#C0392B'],
    ['Software Testing – SWT (Sør-Øst)', 'Testing, testautomatisering, kvalitetssikring og QA. Står bak testkonferansen ODIN.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#27AE60'],
    ['eHelse (Midt-Nord)', 'E-helse, velferdsteknologi, journalsystemer og helsedata. Region Midt-Nord.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#16A085'],
    ['Trondheim Agile (Midt-Nord)', 'Smidig utvikling, agile metoder, produktledelse, scrum og lean. Trondheim.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#E67E22'],
    ['IT-sikkerhet (Vest)', 'IT-sikkerhet, offensiv sikkerhet, hendelseshåndtering og beredskap. Bergen. Søknadsbasert.', Space::JOIN_POLICY_APPLICATION, Space::VISIBILITY_REGISTERED_ONLY, '#8E44AD'],
    ['Skytjenester (Sør-Øst)', 'Sky, cloud-arkitektur, Azure, AWS, kostnadsstyring og plattformteam.', Space::JOIN_POLICY_FREE, Space::VISIBILITY_REGISTERED_ONLY, '#2980B9'],
    ['Fagstyret AI (internt)', 'Lukket arbeidsrom for fagstyret i AI-gruppen. Kun invitasjon.', Space::JOIN_POLICY_NONE, Space::VISIBILITY_NONE, '#34495E'],
];
$spaceModels = [];
foreach ($spaces as [$name, $desc, $join, $vis, $color]) {
    $s = Space::findOne(['name' => $name]);
    if (!$s) {
        $s = new Space();
        $s->name = $name;
        $s->description = $desc;
        $s->join_policy = $join;
        $s->visibility = $vis;
        $s->color = $color;
        $s->created_by = $admin->id;
        if (!$s->save(false)) { $out("FEIL space $name: " . print_r($s->getErrors(), true)); continue; }
        $s->addMember($admin->id, 1, true, Space::USERGROUP_ADMIN);
        $out("Space \"$name\" opprettet");
    }
    $spaceModels[$name] = $s;
}

/* 4. Medlemskap for demobrukere */
if (isset($userModels['kari'], $spaceModels['AI – Kunstig intelligens (Sør-Øst)'])) {
    $spaceModels['AI – Kunstig intelligens (Sør-Øst)']->addMember($userModels['kari']->id, 1, true);
}
if (isset($userModels['ingrid'], $spaceModels['Software Testing – SWT (Sør-Øst)'])) {
    $spaceModels['Software Testing – SWT (Sør-Øst)']->addMember($userModels['ingrid']->id, 1, true);
}
if (isset($userModels['ingrid'], $spaceModels['Trondheim Agile (Midt-Nord)'])) {
    $spaceModels['Trondheim Agile (Midt-Nord)']->addMember($userModels['ingrid']->id, 1, true);
}
/* Ola som søker i Informasjonssikkerhet -> godkjenningskø-demo */
if (isset($userModels['ola.hansen'], $spaceModels['Informasjonssikkerhet (Sør-Øst)'])) {
    $sec = $spaceModels['Informasjonssikkerhet (Sør-Øst)'];
    if (!Membership::findOne(['space_id' => $sec->id, 'user_id' => $userModels['ola.hansen']->id])) {
        $m = new Membership([
            'space_id' => $sec->id,
            'user_id' => $userModels['ola.hansen']->id,
            'status' => Membership::STATUS_APPLICANT,
            'group_id' => Space::USERGROUP_MEMBER,
        ]);
        try { $m->request_message = 'Jobber med personvern og vil gjerne bidra i gruppen.'; } catch (\Throwable $e) {}
        $m->save(false);
        $out('Ola lagt inn som søker i Informasjonssikkerhet');
    }
}

/* 5. Velkomstinnlegg */
$posts = [
    ['AI – Kunstig intelligens (Sør-Øst)', "Velkommen til faggruppen for kunstig intelligens! 🎉\n\nHer deler vi erfaringer med KI i norske virksomheter, planlegger fagkvelder og diskuterer alt fra språkmodeller til regulering. Neste medlemsmøte: torsdag 27. august."],
    ['Software Testing – SWT (Sør-Øst)', "Velkommen til SWT! Programkomiteen for ODIN 2026 søker forresten frivillige – si fra i kommentarfeltet om du vil bidra."],
];
foreach ($posts as [$spaceName, $msg]) {
    if (!isset($spaceModels[$spaceName])) { continue; }
    $s = $spaceModels[$spaceName];
    $exists = Post::find()->joinWith('content')->where(['content.contentcontainer_id' => $s->contentcontainer_id])->exists();
    if ($exists) { continue; }
    try {
        $post = new Post(['message' => $msg]);
        $post->created_by = $admin->id;
        $post->content->setContainer($s);
        $post->content->created_by = $admin->id;
        $post->content->visibility = 1;
        if ($post->save()) { $out("Innlegg opprettet i $spaceName"); }
        else { $out("FEIL innlegg $spaceName: " . print_r($post->getErrors(), true)); }
    } catch (\Throwable $e) {
        $out("Hoppet over innlegg i $spaceName: " . $e->getMessage());
    }
}

$out('Seeding ferdig.');
