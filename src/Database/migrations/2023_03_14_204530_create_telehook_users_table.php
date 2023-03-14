<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelehookUsersTable extends Migration
{
    public function up(): void
    {
        Schema::create('telehook_users', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->unsignedBigInteger('id')->comment('chat id');
            $table->unsignedTinyInteger('is_bot')->nullable()->default(1)->comment('1: true, 0: false');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable();
            $table->string('language_code', 2)->nullable()->default('en');
            $table->string('type')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telehook_users');
    }
}
