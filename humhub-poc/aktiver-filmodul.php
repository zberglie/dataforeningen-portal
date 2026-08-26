<?php
/**
 * Integrasjons-PoC: aktiverer filmodulen (cfiles) i alle faggruppe-spaces,
 * slik at gruppemedlemmer kan dele dokumenter med hverandre (mapper per space,
 * tilgang styrt av space-medlemskap). Idempotent. Forutsetter at modulen er
 * installert: php yii module/install cfiles && php yii module/enable cfiles
 *
 * Kjøres slik (samme mønster som seed.php):
 *   docker compose cp aktiver-filmodul.php humhub:/aktiver-filmodul.php
 *   docker compose exec humhub su www-data -s /bin/bash -c 'php /aktiver-filmodul.php'
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
new humhub\components\console\Application($bootstrap->getConfig('console'));

use humhub\modules\space\models\Space;

foreach (Space::find()->all() as $space) {
    if ($space->moduleManager->isEnabled('cfiles')) {
        print("Allerede aktiv: {$space->name}\n");
    } else {
        $space->moduleManager->enable('cfiles');
        print("Filmodul aktivert: {$space->name}\n");
    }
}
print("Ferdig.\n");
