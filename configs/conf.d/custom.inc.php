<?php

return [
//    'userPin' => true,
//    'userPinMinLength' => 4,
//    'areaLocks' => [
//        'records' => [
//            [
//                'area' => 'MeetingManager',
//                'provider' => 'Pin',
//                'validity' => 'PRESENCE',
//                'lifetime' => 15,
//            ], [
//                'area' => 'ContractManager',
//                'provider' => 'Pin',
//                'validity' => 'PRESENCE',
//                'lifetime' => 15,
//            ],  [
//                'area' => 'Tinebase.datasafe',
//                'provider' => 'Pin',
//                'validity' => 'PRESENCE',
//                'lifetime' => 15,
//            ],
//        ]
//    ],

    'HumanResources' => [
        'features' => [
            'calculateDailyRepots' => true,
            'workingTimeAccounting' => true,
        ],
    ],

    'Bookmarks' => [
        'openBookmarkHooks' => [
            '/board\.pfarrverwaltung\.de/' => 'BoardAuthInjector'
        ]
    ],
    'Calendar' => [
        'features' => [
            'featureYearView' => true,
        ],
    ],
];
