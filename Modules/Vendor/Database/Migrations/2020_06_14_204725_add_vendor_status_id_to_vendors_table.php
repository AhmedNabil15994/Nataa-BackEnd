<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVendorStatusIdToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->integer('vendor_status_id')->unsigned()->after('receive_prescription')->nullable();
            $table->foreign('vendor_status_id')->references('id')->on('vendor_statuses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropForeign('vendors_vendor_status_id_foreign');
            $table->dropIndex('vendors_vendor_status_id_foreign');
            $table->dropColumn('vendor_status_id');
        });
    }
}
