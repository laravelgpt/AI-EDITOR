<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_text_editor_installations', function (Blueprint $table) {
            $table->id();
            $table->string('stack');
            $table->string('theme');
            $table->json('options');
            $table->string('status')->default('pending'); // pending, running, completed, failed
            $table->integer('progress')->default(0);
            $table->text('current_step')->nullable();
            $table->json('logs')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_text_editor_installations');
    }
};
