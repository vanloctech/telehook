# Telehook Laravel

_Telegram bot command for Laravel_

## DEMO

![demo telehook](https://github.com/vanloctech/telehook/blob/master/telehook-demo.gif?raw=true)

## Requirement
- Laravel framework >= 5.8
- PHP >= 7.2

## Reporting Issues

If you do find an issue, please feel free to report it with [GitHub's bug tracker](https://github.com/vanloctech/telehook/issues) for this project.

Alternatively, fork the project and make a pull request :)

## Setup

Install package
```shell
composer require vanloctech/telehook
```

Publish config file `telehook.php` and migration files for project
```shell
php artisan vendor:publish --provider="Vanloctech\Telehook\TelehookServiceProvider"
```

Execute command in schedule for run every minute to stop conversation exceed the time limit
```php
// in app/Console/Kernel.php

// ...

protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('telehook:stop-conversation')->everyMinute();
}

// ...
```

We also provide a facade for Telehook (which has connected using our settings), add following to your `config/app.php` if you need so.
```php
'aliases' => [
    ...
    'Telehook' => \Vanloctech\Telehook\Facades\TelehookFacade::class,
],
```

## Usage
Create telegram command - telehook command
```shell
php artisan make:telehook-command <Command Name>
# Ex: php artisan make:telehook-command HelloWorld
```

Override code in `finish` function
```php
<?php
// TelehookCommand/HelloWorldTelehookCommand.php

namespace App\TelehookCommand;

use Vanloctech\Telehook\Commands\TelehookCommand;

class HelloWorldTelehookCommand extends TelehookCommand
{
    ...

    /**
     * Execute when prepare finish conversation
     *
     * @return void
     */
    public function finish()
    {
        // TODO: Implement finish() method.
    }
}
```

Add command into telehook config file `config/telehook.php`

```php
// config/telehook.php
    'commands' => [
        HelpTelehookCommand::class,
        ...
        
        // add more your command
        \App\TelehookCommand\HelloWorldTelehookCommand::class
    ],
```

using Telehook for send message for multiple chatId
```php
Telehook::init()->setChatId('<chatId>')->sendMessage('your text');
Telehook::init()->setChatId(['<array chatId>'])->sendMessages('your text');
```

Use more function with `telegramApi` property
```php
Telehook::init()->telegramApi->sendPhoto(...);
Telehook::init()->telegramApi->sendDocument(...);
# and more function support call api, referer: https://github.com/irazasyed/telegram-bot-sdk
```

You can set webhook with information setup in `config/telehook.php`:
```php
    /*
    |--------------------------------------------------------------------------
    | Set webhook parameters
    |--------------------------------------------------------------------------
    |
    | specify a url and receive incoming updates via an outgoing webhook.
    | Whenever there is an update for the bot, we will send an HTTPS POST
    | request to the specified url
    | You can set webhook through command is
    | "php artisan telehook:set-webhook"
    |
    */
    'set_webhook' => [
        'url' => env('APP_URL') . '/' . env('TELEHOOK_TOKEN', '')
            . '/' . env('TELEHOOK_PATH', 'webhook'),
        // 'certificate' => env('TELEHOOK_CERTIFICATE', ''),
        // 'ip_address' => '',
        // 'max_connections' => '',
        // 'allowed_updates' => '',
        'drop_pending_updates' => true,
    ],
```

And run command to set webhook:
```shell
php artisan telehook:set-webhook
```

Command set menu command (list of bot's command):
```shell
php artisan telehook:set-command
```

