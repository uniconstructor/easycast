<?php

return [
    'sec-key'  => 'c2109878c-009a-2787-ac66-ab558e8a15e5e1',
    'i18n'     => 'ru',
    'app.name' => 'easyCast',
    'debug'    => true,
    'database' => ["server" => "mongolite://".\Yii::getAlias('@backend/runtime/cockpit/storage')."/data", "options" => ["db" => "cockpitdb"]],
    'memory'   => ["server" => "redislite://".\Yii::getAlias('@backend/runtime/cockpit/storage'."/data/cockpit.memory.sqlite"), "options" => []],
    'paths'    => [
        'storage' => \Yii::getAlias('@backend/runtime/cockpit/storage'),
        '#backups'=> \Yii::getAlias('@backend/runtime/cockpit/storage').'/backups',
        'data'    => \Yii::getAlias('@backend/runtime/cockpit/storage').'/data',
        'custom'  => \Yii::getAlias('@backend/config/cockpit'),
    ],
];