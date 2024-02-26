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
        Schema::create('research_ids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->references('id')->on('offices');
            $table->foreignId('client_id')->references('id')->on('users');
            $table->string('research_code')->unique();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_ids');
    }
};
