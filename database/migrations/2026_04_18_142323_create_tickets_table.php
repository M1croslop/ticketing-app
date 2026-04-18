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

            $table->string('title');
            $table->text('description');

            $table->string('status');
            $table->string('priority');

            // Relaciones
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('agent_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('category_id')->constrained()->onDelete('cascade');

            // Fechas
            $table->timestamp('due_date')->nullable();
            $table->timestamp('resolved_at')->nullable();

            $table->timestamps();
            $table->softDeletes(); // deleted_at
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
