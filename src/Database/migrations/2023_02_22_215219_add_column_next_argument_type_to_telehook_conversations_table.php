<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telehook_conversations', function (Blueprint $table) {
            $table->string('next_argument_type', 20)->nullable();
            $table->string('next_argument_options')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('telehook_conversations', function (Blueprint $table) {
            $table->dropColumn('next_argument_type');
            $table->dropColumn('next_argument_options');
        });
    }
};
