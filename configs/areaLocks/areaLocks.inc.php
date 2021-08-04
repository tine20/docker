<?php

return [
    'areaLocks' => ['records' => [[
        'area_name'         => 'Tasks',
        'areas'             => ['Tasks', 'Addressbook.List', 'Tinebase_datasafe'/*, 'Tinebase_login' /*...Calendar or Calendar.Event.create or Calendar.searchEvent(s?), etc.*/],
        'mfas'              => ['Vodafone', 'hello', 'hotp', 'totp'],
        'validity'          => 'session',
    ]]],
    
    'mfa' => ['records' => [[
        'id'                    => 'Vodafone',
        'provider_config_class' => 'Tinebase_Model_MFA_GenericSmsConfig',
        'provider_config'       => [
            'url' => 'https://shoo.tld/restapi/message',
            'body' => '{"encoding":"auto","body":"{{ message }}","originator":"{{ app.branding.title }}","recipients":["{{ cellphonenumber }}"],"route":"2345"}',
            'method' => 'POST',
            'headers' => [
                'Auth-Bearer' => 'unittesttokenshaaaaalalala'
            ],
            'pin_ttl' => 600,
            'pin_length' => 6,
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_MockSmsAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_SmsUserConfig'
    ], [
        'id'                    => 'hello',
        'provider_config_class' => 'Tinebase_Model_MFA_PinConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_PinAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_PinUserConfig'
    ], [
        'id'                    => 'hotp',
        'provider_config_class' => 'Tinebase_Model_MFA_HTOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_HTOTPUserConfig'
    ], [
        'id'                    => 'totp',
        'provider_config_class' => 'Tinebase_Model_MFA_HTOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_HTOTPUserConfig'
    ]]],
];

