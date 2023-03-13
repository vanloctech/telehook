<?php

namespace Vanloctech\Telehook\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TelehookConversation extends Model
{
    const STATUS_START = 1;
    const STATUS_CHATTING = 2;
    const STATUS_FINISH = 3;
    const STATUS_STOP = 4;

    protected $table = 'telehook_conversations';

    protected $fillable = [
        'chat_id',
        'next_order_send_question',
        'command_name',
        'command_class',
        'status',
        'next_argument_name',
        'next_argument_type',
        'next_argument_options',
        'created_at_bigint',
    ];

    /**
     * Details of conversation
     *
     * @return HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(TelehookConversationDetail::class, 'conversation_id');
    }

    /**
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|TelehookConversationDetail[]
     */
    public function detailsHasArgumentName()
    {
        return TelehookConversationDetail::query()
            ->select(['id', 'argument_name', 'metadata'])
            ->where('conversation_id', $this->id)
            ->where(function (Builder $query) {
                 $query->orWhere('argument_name', '<>', '');
                 $query->orWhereNotNull('argument_name');
            })
            ->get();
    }

    /**
     * List of status to check conversation finish
     *
     * @return int[]
     */
    public static function statusFinish(): array
    {
        return [
            self::STATUS_FINISH,
            self::STATUS_STOP,
        ];
    }

    /**
     * List of status to check conversation working
     * @return int[]
     */
    public static function statusChatting(): array
    {
        return [
            self::STATUS_START,
            self::STATUS_CHATTING,
        ];
    }

    /**
     * Get argument `options`
     *
     * @return array|mixed
     */
    public function getArgumentOptionsAttribute()
    {
        if (empty($this->next_argument_options)) {
            return [];
        }

        return json_decode($this->next_argument_options, true);
    }
}
