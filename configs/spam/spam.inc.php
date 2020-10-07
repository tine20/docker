<?php

return [
    'Felamimail' => [
        'features' => [
            Felamimail_Config::FEATURE_SPAM_SUSPICION_STRATEGY => true,
        ],
        
        'spamSuspicionStrategy' => 'subject',
        
        'spamInfoDialogContent' => 'This message is probably spam.',
    
        'spamSuspicionStrategyConfig' => [
            'pattern' => '/^SPAM\? \([^)]+\) \*\*\* /',
        ],
      
        'spamUserProcessingPipeline' => [
            'spam' => [
                [   // copy mail to central spam processing folder
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            //'accountid' => 'd74368b57ca6f26e853d6d31700818aa895d41ad' , // optional shared account to use instead of own account
                            'folder' => 'Spam' // folder called spam at root level
                        ]
                    ]
                ], 
                [ // copy mail to sent messages 
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            'folder' => '#sent' // use configured sent folder of current user
                        ],
                        'wrap' => [ // original message gets appended as attachment to this wrap
                            'to' => 'sclever@mail.test',
                            'subject' => 'This message is SPAM'
                        ]
                    ]
                ], 
                [ // move mail to trash 
                    'strategy' => 'move',
                    'config' => [
                        'target' => [
                            'folder' => '#trash' // use configured trash folder of current user
                        ],
                    ]
                ]
            ],
        
            'ham' => [
                [   // copy mail to central ham processing folder
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            //'accountid' => 'd74368b57ca6f26e853d6d31700818aa895d41ad', // optional shared account to use instead of own account
                            'folder' => 'Ham' // folder called ham at root level
                        ]
                    ]
                ], 
                [ // copy mail to sent messages 
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            'folder' => '#sent'
                        ],
                        'wrap' => [ // original message gets appended as attachment to this wrap
                            'to' => 'sclever@mail.test',
                            'subject' => 'This message is not SPAM'
                        ]
                    ]
                ], 
                [ // rewrite subject - copys mail with new subject / delete original message
                    'strategy' => 'rewrite_subject',
                    'config' => [
                        'pattern' => '/^SPAM\? \(.+\) \*\*\* /',
                        'replacement' => '',
                    ]
                ]
            ]
        ]
    ]
];
