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
        Schema::create('ticket_status_logs', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');

            $table->foreignId('changed_by')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Estados
            $table->enum('old_status', ['open', 'in_progress', 'resolved', 'closed']);
            $table->enum('new_status', ['open', 'in_progress', 'resolved', 'closed']);

            // Fecha del cambio
            $table->timestamp('changed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_status_logs');
    }
};
