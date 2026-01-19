<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('middleware_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 255)->constrained();
            $table->json('shopify_data')->nullable();
            $table->json('sso_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('middleware_sessions');
    }
};
