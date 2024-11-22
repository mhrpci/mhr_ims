<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToStockTransfersTable extends Migration
{
    public function up()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->foreign('approved_by')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('rejection_reason');
        });
    }
}
