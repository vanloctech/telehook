<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelehookConversationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telehook_conversations', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('chat_id', 50);
            $table->unsignedTinyInteger('next_order_send_question')->nullable()->default(1);
            $table->string('next_argument_name')->nullable();
            $table->string('command_name')->nullable();
            $table->string('command_class')->nullable();
            $table->unsignedTinyInteger('status')->nullable()->default(1);
            $table->unsignedBigInteger('created_at_bigint');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telehook_conversation');
    }
}
