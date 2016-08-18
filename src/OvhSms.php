<?php

namespace Akibatech\Ovhsms;

use Ovh\Sms\SmsApi;

/**
 * Class OvhSms
 *
 * @package Akibatech\Ovhsms
 */
class OvhSms
{
    /**
     * @var array
     */
    private $credentials = [
        'app_key'      => null,
        'app_secret'   => null,
        'consumer_key' => null,
        'endpoint'     => null
    ];

    /**
     * @var string|null
     */
    private $default_account;

    /**
     * @var SmsApi|null
     */
    private $client;

    //-------------------------------------------------------------------------

    /**
     * OvhSms constructor.
     *
     * @param   void
     * @return  self
     */
    public function __construct(array $credentials = [])
    {
        $credentials = collect($credentials);

        if ($credentials->isEmpty() === false)
        {
            $this->credentials = collect($credentials)->only([
                'api_key',
                'app_secret',
                'consumer_key',
                'endpoint'
            ]);
        }
        else
        {
            $this->loadCredentialsFromConfig();
        }

        $this->loadDefaultAccount()
             ->createClient();
    }

    //-------------------------------------------------------------------------

    /**
     * Load API credentials from Laravel config.
     *
     * @param   void
     * @return  self
     */
    protected function loadCredentialsFromConfig()
    {
        $this->credentials['app_key']      = config('laravel-ovh-sms.app_key');
        $this->credentials['app_secret']   = config('laravel-ovh-sms.app_secret');
        $this->credentials['consumer_key'] = config('laravel-ovh-sms.consumer_key');
        $this->credentials['endpoint']     = config('laravel-ovh-sms.endpoint');

        return $this;
    }

    //-------------------------------------------------------------------------

    /**
     * Load the default SMS account from config.
     *
     * @param   void
     * @return  self
     */
    private function loadDefaultAccount()
    {
        $account_id = config('laravel-ovh-sms.sms_account', null);

        if ($account_id)
        {
            $this->default_account = $account_id;
        }

        return $this;
    }

    //-------------------------------------------------------------------------

    /**
     * Create the SmsApi client.
     *
     * @param   void
     * @return  self
     */
    protected function createClient()
    {
        $this->client = new SmsApi(
            $this->credentials['app_key'],
            $this->credentials['app_secret'],
            $this->credentials['endpoint'],
            $this->credentials['consumer_key']
        );

        // A default account is configured
        if ($this->default_account)
        {
            // Get all accounts from API
            $accounts = $this->client->getAccounts();

            // Given default account does not exist.
            if (in_array($this->default_account, $accounts) === false)
            {
                throw new \RuntimeException("Default SMS account does not exist.");
            }

            $this->client->setAccount($this->default_account);
        }

        return $this;
    }

    //-------------------------------------------------------------------------

    /**
     * Returns the OVH API Client.
     *
     * @param   void
     * @return  null|SmsApi
     */
    public function getClient()
    {
        return $this->client;
    }

    //-------------------------------------------------------------------------

    /**
     * Shortcut for creating messages.
     *
     * @param   string|array $to
     * @param   bool         $marketing       Does the message is for marketing?
     * @param   bool         $allowing_answer Does the message accept answers?
     * @return  \Ovh\Sms\Message
     */
    public function newMessage($to, $marketing = false, $allowing_answer = false)
    {
        $message = $this->client->createMessage($allowing_answer);
        $message->setIsMarketing($marketing);

        // Convert receiver to an array of receivers.
        $to = is_array($to) ? $to : [$to];

        foreach ($to as $receiver)
        {
            $message->addReceiver($receiver);
        }

        return $message;
    }

    //-------------------------------------------------------------------------

    /**
     * Prepare a new marketing message.
     *
     * @param   string $to
     * @return  \Ovh\Sms\Message
     */
    public function newMarketingMessage($to)
    {
        return $this->newMessage($to, true, false);
    }

    //-------------------------------------------------------------------------

    /**
     * Send directly a new message.
     *
     * @param   string|array $to
     * @param   string $message
     * @param   bool $marketing
     * @return  array
     * @throws  \Ovh\Exceptions\InvalidParameterException
     */
    public function sendMessage($to, $message, $marketing = false)
    {
        return $this->newMessage($to, $marketing)->send($message);
    }

    //-------------------------------------------------------------------------

    /**
     * Send directly a marketing message.
     *
     * @param   string|array $to
     * @param   string $message
     * @return  array
     * @throws  \Ovh\Exceptions\InvalidParameterException
     */
    public function sendMarketingMessage($to, $message)
    {
        return $this->newMarketingMessage($to)->send($message);
    }

    //-------------------------------------------------------------------------

    /**
     * Dynamic client method call.
     *
     * @param   string $method
     * @param   array  $args
     * @return  SmsApi
     */
    public function __call($method, array $args = [])
    {
        if (method_exists($this->client, $method))
        {
            return call_user_func_array([
                $this->client,
                $method
            ], $args);
        }

        throw new \BadMethodCallException("Invalid method $method.");
    }

    //-------------------------------------------------------------------------
}