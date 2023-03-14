<?php

namespace Vanloctech\Telehook\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelehookUser extends Model
{
    const IS_BOT_TRUE = 1;
    const IS_BOT_FALSE = 0;

    protected $table = 'telehook_users';

    protected $fillable = [
        'uuid',
        'id', // chat_id
        'is_bot',
        'first_name',
        'last_name',
        'username',
        'language_code',
        'type',
    ];
}
