# Telehook Laravel

_Telegram bot command for Laravel_

## Requirement
- Laravel framework >= 5.8
- PHP >= 7.2

## Reporting Issues

If you do find an issue, please feel free to report it with [GitHub's bug tracker](https://github.com/spatie/laravel-fractal/issues) for this project.

Alternatively, fork the project and make a pull request :)

## Setup

Install package
```shell
composer require vanloctech/telehook
```

Publish config file `telehook.php` for project
```shell
php artisan vendor:publish --provider="Vanloctech\Telehook\TelehookServiceProvider"
```

We also provide a facade for elasticsearch-php client (which has connected using our settings), add following to your `config/app.php` if you need so.
```php
'aliases' => [
    ...
    'Telehook' => \Vanloctech\Telehook\Facades\TelehookFacade::class,
],
```

## Usage
Create telegram command
```shell
php artisan make:telegram-command <Command Name>
# Ex: php artisan make:telegram-command HelloWorld
```

Override code in handle function
```php
use \Vanloctech\Telehook\Telehook;

class HelloWorldTelegramCommand extends TelegramCommandAbstract
{
    ...

    /**
     * Execute the command
     *
     * @return void
     */
    public function handle()
    {
        // handle code when get message here
        // something your code
        // Ex: this below code will send message <b>Welcome to my bot chat</b> for chatId sent message
        Telehook::init($this->getChatId())->sendMessage('<b>Welcome to my bot chat</b>');
    }
}
```

Add command into telehook config file `config/telehook.php`
```php
// config/telehook.php
    'commands' => [
        HelpTelegramCommand::class,
        ...
        
        // add more your command
        \App\TelegramCommand\HelloWorldTelegramCommand::class
    ],
```

using Telehook for send message for multiple chatId
```php
Telehook::init()->setChatId('custom array chat id')->sendMessages('your text');
```

Use more function with `telegram` property
```php
Telehook::init()->telegram->sendPhoto(...);
Telehook::init()->telegram->sendDocument(...);
# and more function support call api, referer: https://github.com/irazasyed/telegram-bot-sdk
```

Can set webhook with information setup in `config/telehook.php`:
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
//        'certificate' => env('TELEHOOK_CERTIFICATE', ''),
//        'ip_address' => '',
//        'max_connections' => '',
//        'allowed_updates' => '',
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

