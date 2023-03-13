<?php

namespace Vanloctech\Telehook\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelehookConversationDetail extends Model
{
    const TYPE_RECEIVE = 1;
    const TYPE_RESPONSE = 2;

    protected $table = 'telehook_conversation_details';

    protected $fillable = [
        'message',
        'conversation_id',
        'payload',
        'argument_name',
        'metadata',
        'type',
        'argument_type',
    ];

    /**
     * Conversation of detail
     *
     * @return BelongsTo
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(TelehookConversation::class, 'conversation_id');
    }
}
