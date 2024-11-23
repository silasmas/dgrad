<?php

use App\Models\contreventionUser;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string(column: 'reference')->unique();
            $table->string(column: 'provider_reference')->nullable();
            $table->string(column: 'order_number')->nullable();
            $table->string(column: 'amount_customer')->nullable();
            $table->string(column: 'phone')->nullable();
            $table->string(column: 'currency')->nullable();
            $table->string(column: 'chanel')->nullable();
            $table->foreignIdFor(model: contreventionUser::class)->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string(column: 'etat')->default("0");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
