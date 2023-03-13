<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('telehook_conversation_details', function (Blueprint $table) {
            $table->string('argument_type', 20)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('telehook_conversation_details', function (Blueprint $table) {
            $table->dropColumn('argument_type');
        });
    }
};
