<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_out_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_out_id')->constrained()->onDelete('cascade');
            $table->string('file_name');
            $table->string('original_name');
            $table->string('file_path');
            $table->string('file_type');
            $table->integer('file_size');
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_out_attachments');
    }
}; 