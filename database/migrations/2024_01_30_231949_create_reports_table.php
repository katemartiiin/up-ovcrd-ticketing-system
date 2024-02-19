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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->references('id')->on('users');
            $table->unsignedBigInteger('office_id')->index()->nullable();
            $table->unsignedBigInteger('process_id')->index()->nullable();
            $table->date('from')->index()->nullable();
            $table->date('to')->index()->nullable();
            $table->string('title');
            $table->string('path');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
