<?php

namespace Akibatech\Notifications;

use Akibatech\Ovhsms\OvhSms;
use Illuminate\Notifications\Notification;
use Akibatech\Notifications\OvhSmsMessage;

/**
 * Class OvhSmsChannel
 *
 * @package Akibatech\Notifications
 */
class OvhSmsChannel
{
    /**
     * @var OvhSms
     */
    private $client;

    //-------------------------------------------------------------------------

    /**
     * OvhSmsChannel constructor.
     *
     * @param   OvhSms $client
     * @return  self
     */
    public function __construct(OvhSms $client)
    {
        $this->client = $client;
    }

    //-------------------------------------------------------------------------

    /**
     * Send the given notification.
     *
     * @param   mixed  $notifiable
     * @param   \Illuminate\Notifications\Notification  $notification
     * @return  void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $to = $notifiable->routeNotificationFor('ovh'))
        {
            return;
        }

        $message = $notification->toOvh($notifiable);

        if (is_string($message))
        {
            $message = new OvhSmsMessage($message);
        }

        $sms = $this->client->newMessage($to);

        $sms->send($message);
    }

    //-------------------------------------------------------------------------
}
