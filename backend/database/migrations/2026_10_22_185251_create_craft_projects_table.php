<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('craft_projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('category');

            $table->text('reference_image_url')->nullable();
            $table->text('tutorial_url')->nullable();

            $table->string('status')->default('not_started');
            $table->unsignedTinyInteger('progress')->default(0);

            $table->date('start_date');
            $table->date('target_date')->nullable();

            $table->string('difficulty')->nullable();
            $table->text('materials')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('craft_projects');
    }
};
