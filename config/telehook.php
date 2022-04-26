<?php

use Vanloctech\Telehook\Commands\DefaultTelegramCommand;
use Vanloctech\Telehook\Commands\ExampleTelegramCommand;
use Vanloctech\Telehook\Commands\HelpTelegramCommand;

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram bot api URL
    |--------------------------------------------------------------------------
    |
    | All queries to the Telegram Bot API must be served over HTTPS
    | and need to be presented
    |
    */
    'api_url' => 'https://api.telegram.org/bot',

    /*
    |--------------------------------------------------------------------------
    | Unique authentication token of telegram bot
    |--------------------------------------------------------------------------
    |
    | Each bot is given a unique authentication token when it is created.
    | The token looks something like 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11
    |
    */
    'token' => env('TELEHOOK_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Webhook endpoint path
    |--------------------------------------------------------------------------
    |
    | Endpoint of URI webhook
    | URI of webhook will https://YOUR_APP_URL/<token>/webhook
    | Note: telegram webhook only allow https
    | You can use ngrok for test in local, referer: https://ngrok.com/
    |
    */
    'path' => env('TELEHOOK_PATH', 'webhook'),

    /*
    |--------------------------------------------------------------------------
    | Character of command
    |--------------------------------------------------------------------------
    |
    | You can use any character to define it as a command when the user sends a message
    | Recommend use "/"
    |
    */
    'character_command' => '/',

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

    /*
    |--------------------------------------------------------------------------
    | Unknown message response
    |--------------------------------------------------------------------------
    |
    | Message response to the user when the command
    | is not syntactically correct (not defined in the system)
    | You can use "{command}" for represent the name of command
    |
    */
    'unknown_message' => 'Unknown command, try /help to see a list of commands',

    /*
    |--------------------------------------------------------------------------
    | Missing argument message response
    |--------------------------------------------------------------------------
    |
    | Message response to the user when the command
    | does not have enough predefined input parameters.
    | You can use "{command}" for represent the name of command
    |
    */
    'missing_args_message' => 'Argument in command {command} is missing, try /help to see a list of commands',

    /*
    |--------------------------------------------------------------------------
    | Class handle not command
    |--------------------------------------------------------------------------
    |
    | Here you can define the class to handle when a message
    | other than a command is sent
    |
    */
    'default' => DefaultTelegramCommand::class,

    /*
    |--------------------------------------------------------------------------
    | Commands of bot
    |--------------------------------------------------------------------------
    |
    | The command list here will be checked and processed when a command is called.
    | You can add TelegramCommand classes to create a new command flow.
    | Command to create TelegramCommand is
    | "php artisan make:telegram-command <name of command>"
    | Ex: php artisan make:telegram-command HelloWorld
    | After create new TelegramCommand you can run command to set(re-set)
    | the list of the bot's commands in Telegram application
    |
    */
    'commands' => [
        HelpTelegramCommand::class,
        ExampleTelegramCommand::class,

        // add more your command
    ],

];
