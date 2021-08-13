<?php

return [
    'areaLocks' => ['records' => [[
        'area_name'         => 'Tasks',
        'areas'             => ['Tasks', 'Addressbook.List', 'Tinebase_datasafe'/*, 'Tinebase_login' /*...Calendar or Calendar.Event.create or Calendar.searchEvent(s?), etc.*/],
        'mfas'              => ['Vodafone', 'pin', 'hotp', 'totp'],
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
        'id'                    => 'pin',
        'provider_config_class' => 'Tinebase_Model_MFA_PinConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_PinAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_PinUserConfig'
    ], [
        'id'                    => 'hotp',
        'provider_config_class' => 'Tinebase_Model_MFA_HOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_HOTPUserConfig'
    ], [
        'id'                    => 'totp',
        'provider_config_class' => 'Tinebase_Model_MFA_TOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_TOTPUserConfig'
    ], [
        'id'                    => 'webauthn',
        'provider_config_class' => 'Tinebase_Model_MFA_WebAuthnConfig',
        'provider_config'       => [
            'authenticator_attachment' => 'cross-platform', // may be null, platform, cross-platform
            'user_verification_requirement' => 'preferred', // may be required, preferred, discouraged
            'resident_key_requirement' => null, // may be null, required, preferred, discouraged
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_WebAuthnAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_WebAuthnUserConfig'
    ]]],
];

