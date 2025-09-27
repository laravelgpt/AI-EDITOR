<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feature_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feature_id')->constrained('dynamic_features')->onDelete('cascade');
            $table->string('version'); // 1.0.0, 1.1.0, etc.
            $table->text('description');
            $table->json('code_snapshot'); // Code at this version
            $table->boolean('is_active')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['feature_id', 'version']);
            $table->index(['feature_id', 'is_active']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feature_versions');
    }
};
