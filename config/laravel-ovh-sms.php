<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OVH SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Create credentials at: https://api.ovh.com/createToken/index.cgi?GET=/sms&GET=/sms/*&PUT=/sms/*&DELETE=/sms/*&POST=/sms/*
    | More documentation at: https://github.com/ovh/php-ovh-sms
    |
    */

    /**
     * Your app key
     */
    'app_key'      => env('OVHSMS_APP_KEY', 'your_app_key'),

    /**
     * Your app secret
     */
    'app_secret'   => env('OVHSMS_APP_SECRET', 'you_app_secret'),

    /**
     * Your consumer key
     */
    'consumer_key' => env('OVHSMS_CONSUMER_KEY', 'your_consumer_key'),

    /**
     * SMS Enpoint
     */
    'endpoint'     => env('OVHSMS_ENDPOINT', 'ovh-eu'),

    /**
     * Your default SMS account.
     * Ex: sms-LLXXXXX-X
     */
    'sms_account'  => env('OVHSMS_ACCOUNT', 'sms'),

    /**
     * Your API user login
     */
    'sms_user_login' => env('OVHSMS_USER_LOGIN'),

    /**
     * Your default SMS sender
     */
    'sms_default_sender' => env('OVHSMS_SENDER')

];