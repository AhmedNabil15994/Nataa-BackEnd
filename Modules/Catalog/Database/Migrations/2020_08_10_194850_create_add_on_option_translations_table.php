<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAddOnOptionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('add_on_option_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('add_on_option_id')->unsigned();
            $table->string('option');
            $table->string('locale')->index();
            $table->foreign('add_on_option_id')->references('id')->on('add_on_options')->onDelete('cascade');
            $table->unique(['add_on_option_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('add_on_option_translations');
    }
}
