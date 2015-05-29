<?php

return [
    'sec-key'  => 'c2109878c-009a-2787-ac66-ab558e8a15e5e1',
    'i18n'     => 'ru',
    'app.name' => 'easyCast',
    'debug'    => false,
    'database' => ["server" => "mongolite://".("/cockpitdb/storage"."/data"), "options" => ["db" => "cockpitdb"]],
    'memory'   => ["server" => "redislite://".("/cockpitdb/storage"."/data/cockpit.memory.sqlite"), "options" => []],
    'paths'    => [
        'storage'  => '/cockpitdb/storage',
        '#backups' => '/cockpitdb/storage/backups',
        'data'     => '/cockpitdb/storage/data',
        'custom'  => \Yii::getAlias('@backend/config/cockpit'),
    ],
];