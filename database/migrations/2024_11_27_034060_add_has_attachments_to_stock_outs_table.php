<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->boolean('has_attachments')->default(false);
        });
    }

    public function down()
    {
        Schema::table('stock_outs', function (Blueprint $table) {
            $table->dropColumn('has_attachments');
        });
    }
}; 