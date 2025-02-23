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
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner');
            $table->unsignedBigInteger('organization')->nullable();
            $table->unsignedBigInteger('job')->nullable();
            $table->unsignedBigInteger('candidate')->nullable();
            $table->unsignedBigInteger('creator')->nullable();
            $table->string('stage_name')->nullable();
            $table->boolean('is_active')->nullable();
            $table->timestamp('hired_at')->useCurrent()->onUpdate('current_timestamp');
            $table->timestamp('submitted_at')->nullable(); // Change the default value
            $table->timestamp('interview_at')->nullable();
            $table->timestamp('offer_at')->nullable();
            $table->timestamp('dropped_at')->nullable();
            $table->timestamp('created_at_editable')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->onUpdate('current_timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
