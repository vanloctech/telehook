<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelehookConversationDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telehook_conversation_details', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('conversation_id');
            $table->text('message')->nullable();
            $table->string('argument_name')->nullable();
            $table->text('metadata')->nullable();
            $table->unsignedTinyInteger('type')->nullable()
                ->default(1)
                ->comment('1: receive, 2: response');
            $table->text('payload')->nullable();
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
        Schema::dropIfExists('telehook_conversation_detail');
    }
}
