<?php

use humhub\modules\dashboard\widgets\Sidebar;

return [
    'id' => 'dnd-forslag',
    'class' => 'dndforslag\Module',
    'namespace' => 'dndforslag',
    'events' => [
        [Sidebar::class, Sidebar::EVENT_INIT, ['dndforslag\Events', 'onDashboardSidebarInit']],
    ],
];
