<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_features', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type'); // crud, api, dashboard, component, integration, custom
            $table->string('category'); // authentication, content_management, ecommerce, etc.
            $table->boolean('is_enabled')->default(true);
            $table->boolean('is_ai_generated')->default(false);
            $table->json('configuration'); // Feature configuration
            $table->json('code_snapshot'); // Generated code
            $table->json('metadata')->nullable(); // AI metadata, version info, etc.
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->index(['category', 'type']);
            $table->index(['is_enabled', 'is_ai_generated']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_features');
    }
};
