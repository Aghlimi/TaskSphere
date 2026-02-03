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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->morphs('invitable');
            $table->index(['invitable_type', 'invitable_id']);

            $table->foreignId('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('sender_id');
            $table->foreignId('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
