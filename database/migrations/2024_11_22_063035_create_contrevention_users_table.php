<?php

use App\Models\contrevention;
use App\Models\User;
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
        Schema::create('contrevention_users', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(contrevention::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->foreignIdFor(model: User::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('matricule');
            $table->string(column: 'reference')->unique();
            $table->string(column: 'payerPar')->nullable();
            $table->string(column: 'phone')->nullable();
            $table->string('etat')->default("0");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contrevention_users');
    }
};
