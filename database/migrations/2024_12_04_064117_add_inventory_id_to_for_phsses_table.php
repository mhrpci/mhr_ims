<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryIdToForPhssesTable extends Migration
{
    public function up()
    {
        Schema::table('for_phsses', function (Blueprint $table) {
            $table->foreignId('inventory_id')->after('product_id')->constrained();
        });
    }

    public function down()
    {
        Schema::table('for_phsses', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn('inventory_id');
        });
    }
} 