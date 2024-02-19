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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('control_number')->index()->unique();
            $table->foreignId('office_id')->references('id')->on('offices');
            $table->foreignId('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('staff_id')->index()->nullable();
            $table->foreignId('process_id')->references('id')->on('processes');
            $table->unsignedBigInteger('research_id')->index()->nullable();
            $table->string('title');
            $table->text('description');
            $table->tinyInteger('status')->index()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
