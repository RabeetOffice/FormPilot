<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->enum('lead_temperature', ['hot', 'warm', 'cold'])->nullable();
            $table->string('service_type')->nullable();
            $table->decimal('spam_probability', 5, 4)->default(0);
            $table->enum('urgency', ['high', 'medium', 'low'])->nullable();
            $table->enum('sentiment', ['positive', 'neutral', 'negative'])->nullable();
            $table->text('summary')->nullable();
            $table->text('routing_recommendation')->nullable();
            $table->json('raw_response')->nullable();
            $table->string('model_used')->default('rule_based');
            $table->timestamps();

            $table->unique('submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_classifications');
    }
};
