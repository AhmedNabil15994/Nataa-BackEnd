<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatLongToAddressesTable extends Migration
{
    const TABLES = ["addresses","order_addresses","unknown_order_address"];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach(self::TABLES as $tableName){

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('lat')->nullable();
                $table->string('long')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach(self::TABLES as $tableName){
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['lat','long']);
            });
        }
    }
}
