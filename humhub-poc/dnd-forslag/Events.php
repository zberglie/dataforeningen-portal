<?php

namespace dndforslag;

use Yii;

class Events
{
    public static function onDashboardSidebarInit($event)
    {
        if (!Yii::$app->user->isGuest) {
            $event->sender->addWidget(widgets\ForslagWidget::class, [], ['sortOrder' => 5]);
        }
    }
}
