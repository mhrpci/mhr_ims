<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('receiving_reports', function (Blueprint $table) {
            $table->dropUnique(['barcode']); // Remove the unique constraint
        });
    }

    public function down()
    {
        Schema::table('receiving_reports', function (Blueprint $table) {
            $table->unique('barcode'); // Add back the unique constraint if needed
        });
    }
}; 