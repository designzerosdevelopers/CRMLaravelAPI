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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('organization_id')->nullable();
            $table->string('job_title')->nullable();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->integer('zipcode')->nullable();
            $table->string('status')->default('Active');
            $table->string('is_published')->nullable();
            $table->string('is_remote')->nullable();
            $table->text('skill')->nullable();
            $table->text('experience')->nullable();
            $table->unsignedBigInteger('degree_id')->nullable();
            $table->text('budget')->nullable();
            $table->text('bid_close')->nullable();
            $table->text('deadline')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->string('career_page_url')->nullable();
            $table->boolean('is_pinned_in_career_page')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
