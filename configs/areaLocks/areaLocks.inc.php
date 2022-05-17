<?php

return [
    'areaLocks' => ['records' => [[
        'area_name'         => 'Login',
        'areas'             => ['Tinebase_login'],
        'mfas'              => ['sms', 'pin', 'hotp', 'totp', 'webauthn'],
        'validity'          => 'session',
    ], [
        'area_name'         => 'Tasks',
        'areas'             => ['Tasks', 'Addressbook.List', 'Tinebase_datasafe' /*...Calendar or Calendar.Event.create or Calendar.searchEvent(s?), etc.*/],
        'mfas'              => ['sms', 'pin', 'hotp', 'totp', 'webauthn'],
        'validity'          => 'presence',
        'lifetime'          => 3,
    ]]],
    
    'mfa' => ['records' => [[
        'id'                    => 'sms',
        'provider_config_class' => 'Tinebase_Model_MFA_GenericSmsConfig',
	'allow_self_service'    => true,
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
	'allow_self_service'    => true,
        'provider_config_class' => 'Tinebase_Model_MFA_PinConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_PinAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_PinUserConfig'
    ], [
        'id'                    => 'hotp',
	'allow_self_service'    => true,
        'provider_config_class' => 'Tinebase_Model_MFA_HOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_HOTPUserConfig'
    ], [
	'id'                    => 'totp',
	'allow_self_service'    => true,
        'provider_config_class' => 'Tinebase_Model_MFA_TOTPConfig',
        'provider_config'       => [
            
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_HTOTPAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_TOTPUserConfig'
    ], [
        'id'                    => 'webauthn',
        'allow_self_service'    => true,
        'provider_config_class' => 'Tinebase_Model_MFA_WebAuthnConfig',
        'provider_config'       => [
            'authenticator_attachment' => null, // may be null, platform, cross-platform
            'user_verification_requirement' => 'required', // may be required, preferred, discouraged
            'resident_key_requirement' => null, // may be null, required, preferred, discouraged
        ],
        'provider_class'        => 'Tinebase_Auth_MFA_WebAuthnAdapter',
        'user_config_class'     => 'Tinebase_Model_MFA_WebAuthnUserConfig'
    ]]],
];

