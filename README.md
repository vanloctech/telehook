# Telehook Laravel

_Telegram bot command like conversation for Laravel_

## DEMO

https://user-images.githubusercontent.com/20141611/224770232-a8681de5-b6e2-46e4-b4cc-b4088ce155a5.mp4

## Requirement
- Laravel framework >= 5.8 and up
- PHP >= 7.3

## Reporting Issues

If you do find an issue, please feel free to report it with [GitHub's bug tracker](https://github.com/vanloctech/telehook/issues) for this project.

Alternatively, fork the project and make a pull request :)

## Setup

Install package
```shell
composer require vanloctech/telehook
```

Publish config file `telehook.php`, migration files, translations file for project
```shell
php artisan vendor:publish --provider="Vanloctech\Telehook\TelehookServiceProvider"
```

Execute command in schedule for run every minute to stop conversation exceed the time limit
```php
// app/Console/Kernel.php

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
// config/app.php

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
// any files

Telehook::init('<chat_id>')->telegramApi->sendPhoto(...);
Telehook::init('<chat_id>')->telegramApi->sendDocument(...);
# and more function support call api, referer: https://github.com/irazasyed/telegram-bot-sdk
# <chat_id> you can get in `telehook_users` table through `TelehookUser` model
```

### How to setup in the project
Declare **https** `URL` in `.env` file (`APP_URL`), (because telegram webhook requires **https**):

**You can use ngrok ([https://ngrok.com](https://ngrok.com)) to make `https` URL

```dotenv
# .env

APP_URL=https://<your app url>
```

Next, you can set webhook with information setup in `config/telehook.php` or setup in `.env` file:
```php
// config/telehook.php

    /*
    |--------------------------------------------------------------------------
    | Unique authentication token of telegram bot
    |--------------------------------------------------------------------------
    */
    'token' => env('TELEHOOK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Set webhook parameters
    |--------------------------------------------------------------------------
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

# output example
Your URI webhook: https://<your url webhook>
Set webhook successfully.
```

Command set menu command (list of bot's command):
```shell
php artisan telehook:set-command

# output example
+---------+-----------------------------------+
| command | description                       |
+---------+-----------------------------------+
| help    | Display list of the bot's command |
| example | Example command                   |
| stop    | Stop conversation                 |
+---------+-----------------------------------+
```

You can setup a schedule for clear conversation finished:
```php
// app/Console/Kernel.php

// ...

protected function schedule(Schedule $schedule)
{
    // ...
    $schedule->command('telehook:clear --chunk=1000')->dailyAt('01:00');
}

// ...
```
