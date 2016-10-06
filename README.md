# Laravel OVH SMS

This is an unofficial integration of the [php-ovh-sms](https://github.com/ovh/php-ovh-sms) library for Laravel 5.  

- Original [PHP OVH SMS library](https://github.com/ovh/php-ovh-sms/blob/master/README.md)
- Plans & pricing (20 free credits) on the [official site](https://www.ovhtelecom.fr/sms/)
- Getting credentials on the [OVH Api Explorer](https://api.ovh.com/createToken/index.cgi?GET=/sms&GET=/sms/*&PUT=/sms/*&DELETE=/sms/*&POST=/sms/*)

## Summary

- [Installation](#installation)
- [Usage](#usage)
    - [Package API workflow](#package-api-workflow)
    - [Original API workflow](#original-api-workflow)
- [Using with Laravel Notifications](#using-with-laravel-notifications)
    - [Example notification](#example-notification)
- [Getting credentials](#getting-credentials)
- [Support](#support)
- [Licence](#licence)

## Installation

Require this package with composer:  
```bash
composer require akibatech/laravel-ovh-sms dev-master
```

After updating composer, add the ServiceProvider to the **providers** array in config/app.php:  
```php
Akibatech\Ovhsms\ServiceProvider::class,
```

If you want to use the Facade for rapid message sending, you can add this line to your config/app.php in the **aliases** section:  
```php
'Ovhsms' => Akibatech\Ovhsms\Facade::class,
```

Then, you should publish the **laravel-ovh-sms** to your config folder with the following command.  
```bash
php artisan vendor:publish --provider="Akibatech\Ovhsms\ServiceProvider"
```

## Usage

Send a message (using Facade) anywhere in your app:
```php
Ovhsms::sendMessage('+33611223344', 'Hello!');
```

Send a message in a controller using DI:
```php
public function myControllerAction(Akibatech\Ovhsms\OvhSms $client)
{
    $client->sendMessage('+33611223344', 'Hello!');
}
```

### Package API workflow

This package give you an access to a ready to use **Ovh\Sms\SmsApi** instance with your configured credentials and your default sms account (if present).

It also offer some helpers over the original Api.  

```php
$client = app('ovhsms');

// Prepare a new SMS instance and return it.
$sms = $client->newMessage('the phone number');
$sms->send('Hi!');

// Same as above but the SMS is marked as a marketing message.
$sms = $client->newMarketingMessage($phone); // Alias of newMessage($phone, true);
$sms->send('Hello!');

// Attach many receivers
$sms = $client->newMessage(['phone1', 'phone2'], ...);
$sms->send('Hi guys!');

// Send directly the message
$client->sendMessage($phone, 'Hello!');
// Or
$client->sendMarketingMessage($phone, 'Super price this sunday!');
```

### Original API workflow

If you don't want to use ready-to-use helpers, you can follow the original workflow. Here's an example:  
```php
// Retrieve OVH SMS instance
$ovhsms = app('ovhsms'); // Or Ovhsms::getClient();

// Get available SMS accounts
$accounts = $ovhsms->getAccounts();

// Set the account you will use
$ovhsms->setAccount($accounts[0]);

// Create a new message that will allow the recipient to answer (to FR receipients only)
$sms = $ovh->createMessage(true);
$sms->addReceiver("+33601020304");
$sms->setIsMarketing(false);

// Plan to send it in the future
$sms->setDeliveryDate(new DateTime("2018-02-25 18:40:00"));
$sms->send("Hello world!");
```

## Using with Laravel Notifications

This package can be used as a driver for Laravel Notifications (Laravel >= 5.3).  

### Example notification

Here's a simple notification example.  

```php
namespace App\Notifications;

use Akibatech\Ovhsms\Notifications\OvhSmsChannel;
use Akibatech\Ovhsms\Notifications\OvhSmsMessage;
use Illuminate\Notifications\Notification;

class ExampleNotification extends Notification
{
    /**
     * Notification via OvhSmsChannel.
     */
    public function via($notifiable)
    {
        return [OvhSmsChannel::class];
    }

    /**
     * Your notification must implements "toOvh()"
     */
    public function toOvh($notifiable)
    {
    	return (new OvhSmsMessage('A new invoice was paid! Amount: $9.00'));
    }
}
```

Also, your Notifiable model must implements **routeNotificationForOvh()**.  

```php
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    
    /**
     * Returns the user's phone number.
     */
    public function routeNotificationForOvh()
    {
        return $this->phone; // Ex: +33611223344
    }
}
```

Nice, you're ready to use the new Laravel Notifications system.

## Getting credentials

You can get your credentials from the [official API Explorer site](https://api.ovh.com/createToken/index.cgi?GET=/sms&GET=/sms/*&PUT=/sms/*&DELETE=/sms/*&POST=/sms/*) at OVH.  

Once your credentials in hands, you need to put them in **config/laravel-ovh-sms.php**.  
For convenience, you can put them in your .env file.    

Config keys are:
- OVHSMS_APP_KEY => your application key
- OVHSMS_APP_SECRET => your application secret
- OVHSMS_CONSUMER_KEY => your consumer key
- OVHSMS_ENDPOINT => your endpoint (defaults to ovh-eu)

Optional keys:  
- OVHSMS_ACCOUNT => your sms account ID (formatted like "sms-LLXXXXX-X")
- OVHSMS_USER_LOGIN => your API user ID
- OVHSMS_SENDER => phone number or alphanumeric sender designation

## Support

Issues related to **ovh/php-ovh-sms** should be posted on [its own repo](https://github.com/ovh/php-ovh-sms).  
For this Laravel package, feel free to post your issues in the issues section.  

## Licence

MIT
