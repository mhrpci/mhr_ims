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
            $table->string('report_number')->unique();
            $table->string('report_type');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->datetime('date_from');
            $table->datetime('date_to');
            $table->foreignId('generated_by')->constrained('users');
            $table->json('data');
            $table->json('parameters')->nullable();
            $table->integer('total_records')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('completed');
            $table->timestamps();
            
            $table->index(['report_type', 'date_from', 'date_to']);
            $table->index('branch_id');
            $table->index('generated_by');
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
