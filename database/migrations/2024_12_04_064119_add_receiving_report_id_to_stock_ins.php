<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivingReportIdToStockIns extends Migration
{
    public function up()
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->foreignId('receiving_report_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropForeign('receiving_report_id');
            $table->dropColumn('receiving_report_id');
        });
    }
} 