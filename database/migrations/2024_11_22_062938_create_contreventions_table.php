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
        Schema::create('contreventions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string(column: 'slug')->unique();
            $table->string(column: 'description')->nullable();
            $table->string(column: 'prix');
            $table->string('monaie');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contreventions');
    }
};
