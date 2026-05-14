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

        // Quién crea el ticket (usuario municipal)
        $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

        // A quién se asigna (técnico) - puede ser null
        $table->foreignId('assigned_to')->nullable();
	$table->foreign('assigned_to')->references('id')->on('users'); // NO ACTION por defecto en SQL Server


        // Datos del ticket
        $table->string('subject', 200);
        $table->text('description');

        // Catálogo simple
        $table->string('category', 50);     // hardware, software, red, sistemas, otros
        $table->string('priority', 20);     // baja, media, alta, critica
        $table->string('status', 20)->default('abierto'); // abierto, en_proceso, resuelto, cerrado

        // Opcional: seguimiento
        $table->timestamp('resolved_at')->nullable();
        $table->timestamp('closed_at')->nullable();

        $table->timestamps();

        // Índices útiles
        $table->index(['status', 'priority']);
        $table->index(['created_by']);
        $table->index(['assigned_to']);
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
