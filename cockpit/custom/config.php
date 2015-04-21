<?php

return array(
    //'media.path' => 's3://media.easycast.ru/cockpit',
    //'docs_root'  => 's3://data.easycast.ru/cockpit',
    'sec-key'  => 'c2109878c-009a-2787-ac66-ab558e8a15e5e1',
    'i18n'     => 'ru',
    'app.name' => 'easyCast',
    'database' => [ "server" => "mongolite://".("/cockpitdb/storage/data"), "options" => ["db" => "cockpitdb"] ],
    'memory'   => [ "server" => "redislite://".("/cockpitdb/storage/data/cockpit.memory.sqlite"), "options" => [] ],
    'paths' => array(
        'storage'  => '/cockpitdb/storage',
        'backups'  => '/cockpitdb/storage/backups',
        '#backups' => '/cockpitdb/storage/backups',
        'data'     => '/cockpitdb/storage/data',
        //'cache'    => '/cockpitdb/storage/cache',
        //'tmp'      => '/cockpitdb/storage/cache/tmp',
    ),
);
    /*'database'  => [ 
        "server" => "mongodb://127.0.0.1:27017", 
        "options" => [
            "db"       => "cockpitdb",
            "username" => "bitnami",
            "password" => "b4zCye2X18qZ",
        ],
    ],*/