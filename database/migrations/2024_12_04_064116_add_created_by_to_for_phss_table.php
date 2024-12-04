<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedByToForPhssTable extends Migration
{
    public function up()
    {
        Schema::table('for_phsses', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users');
        });
    }

    public function down()
    {
        Schema::table('for_phsses', function (Blueprint $table) {
            $table->dropForeign('created_by');
            $table->dropColumn('created_by');
        });
    }
} 