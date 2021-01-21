<?php

return [
    'Felamimail' => [
        'features' => [
            'featureSpamSuspicionStrategy' => TRUE,
        ],
        
        'spamSuspicionStrategy' => 'subject',
        
        // this should be changed in ansible in the future
        'spamInfoDialogContent' => 
            'Diese Nachricht wird vom System als eine mögliche Spammail klassifiziert.<br/>
            <br/>
            Sie haben die Möglichkeit diese Mail als „Ja es ist Spam“ abzuweisen und damit zu löschen.<br/>
            <br/>
            Oder Sie können mit der Antwort „Nein kein Spam“ dem System mitzuteilen, dass es sich hierbei um keinen Spammail handelt. 
            Die Spamverdachtsmarkierung wird dann entfernt und Sie können die Mail normal weiterverarbeiten.<br/>
            <br/>
            Der Filter lernt mit Ihrer Entscheidung und wird zukünftige ähnliche E-Mails automatisch als Spam behandeln können.',
    
        'spamSuspicionStrategyConfig' => [
            'pattern' => '/^SPAM\? \(.+\) \*\*\* /',
        ],
      
        'spamUserProcessingPipeline' => [
            'spam' => [
                [   // copy mail to central spam processing folder
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            //'accountid' => 'd74368b57ca6f26e853d6d31700818aa895d41ad' , // optional shared account to use instead of own account
                            'folder' => 'INBOX/SPAM' // folder called spam at root level
                        ]
                    ]
                ], 
                [   // copy mail to local directory
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            'local_directory' => '/var/lib/tine20/rspamd/spam/'
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
                            'folder' => 'INBOX/HAM' // folder called ham at root level
                        ]
                    ]
                ], 
                [   // copy mail to local directory
                    'strategy' => 'copy',
                    'config' => [
                        'target' => [
                            'local_directory' => '/var/lib/tine20/rspamd/ham/'
                        ]
                    ]
                ],   
                [ // rewrite subject - copies mail with new subject / delete original message
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
