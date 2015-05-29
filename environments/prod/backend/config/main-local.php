<?php
return [
    'components' => [
        'request' => [
            'cookieValidationKey' => 'dbZo3EnKEwGRgz2gVkoEYbVhNintGvZC',
        ],
        'cockpit' => [
            'cockpitStoragePath' => '/cockpitdb/storage',
            'config' => [
                'paths' => [
                    'storage'  => '/cockpitdb/storage',
                    '#backups' => '/cockpitdb/storage/backups',
                    'data'     => '/cockpitdb/storage/data',
                ],
            ],
        ],
    ],
];
