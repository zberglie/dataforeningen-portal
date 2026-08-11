<?php

namespace dndforslag\widgets;

use Yii;
use humhub\components\Widget;
use humhub\modules\space\models\Membership;
use humhub\modules\space\models\Space;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Enkel interessematching: tokeniserer profilfeltet "interesser" og scorer
 * synlige spaces på treff i navn + beskrivelse. Toppkandidatene vises som
 * "Forslag til deg" i dashbordets sidekolonne.
 */
class ForslagWidget extends Widget
{
    public function run()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            return '';
        }

        $interesser = trim((string)($user->profile->interesser ?? ''));
        $tokens = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', mb_strtolower($interesser)))));

        $memberSpaceIds = Membership::find()->select('space_id')->where(['user_id' => $user->id])->column();

        $scored = [];
        if ($tokens) {
            $spaces = Space::find()
                ->where(['status' => Space::STATUS_ENABLED])
                ->andWhere(['!=', 'visibility', Space::VISIBILITY_NONE])
                ->all();
            foreach ($spaces as $space) {
                if (in_array($space->id, $memberSpaceIds)) {
                    continue;
                }
                $hay = mb_strtolower($space->name . ' ' . (string)$space->description);
                $hits = [];
                foreach ($tokens as $t) {
                    if ($t !== '' && preg_match('/(?<![\pL\pN])' . preg_quote($t, '/') . '(?![\pL\pN])/u', $hay)) {
                        $hits[] = $t;
                    }
                }
                if ($hits) {
                    $scored[] = ['space' => $space, 'hits' => $hits];
                }
            }
            usort($scored, fn($a, $b) => count($b['hits']) <=> count($a['hits']));
            $scored = array_slice($scored, 0, 4);
        }

        $html = '<div class="panel panel-default"><div class="panel-heading">'
            . '<strong>Forslag til deg</strong><br><span class="text-muted" style="font-size:11px">basert p&aring; interessene i profilen din</span>'
            . '</div><div class="panel-body" style="padding-top:8px">';

        if (!$tokens) {
            $html .= '<p style="font-size:12px;margin:0">Legg inn interesser i '
                . Html::a('profilen din', Url::to(['/user/account/edit']))
                . ', s&aring; foresl&aring;r vi faggrupper for deg.</p>';
        } elseif (!$scored) {
            $html .= '<p style="font-size:12px;margin:0">Ingen nye grupper matcher interessene dine (' . Html::encode($interesser) . ') akkurat n&aring;.</p>';
        } else {
            foreach ($scored as $row) {
                /** @var Space $space */
                $space = $row['space'];
                $policy = $space->join_policy == Space::JOIN_POLICY_FREE
                    ? '<span class="label label-success">&Aring;pen</span>'
                    : '<span class="label label-warning">S&oslash;knad</span>';
                $html .= '<div style="margin-bottom:10px">'
                    . '<div>' . Html::a(Html::encode($space->name), $space->getUrl(), ['style' => 'font-weight:600;font-size:13px']) . '</div>'
                    . '<div style="font-size:11px" class="text-muted">Treff: ' . Html::encode(implode(', ', $row['hits'])) . ' &middot; ' . $policy . '</div>'
                    . '</div>';
            }
        }

        $html .= '</div></div>';
        return $html;
    }
}
