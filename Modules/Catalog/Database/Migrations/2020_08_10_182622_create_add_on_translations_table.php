<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddOnTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_on_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('add_on_id')->unsigned();
            $table->string('name');
            $table->string('locale')->index();
            $table->foreign('add_on_id')->references('id')->on('add_ons')->onDelete('cascade');
            $table->unique(['add_on_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_on_translations');
    }
}
