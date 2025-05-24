<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permission_translations', function (Blueprint $table) {
          $table->increments('id');
          $table->text('description')->nullable();
          $table->string('locale')->index();
          $table->integer('permission_id')->unsigned();
          $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
          $table->unique(['permission_id','locale']);
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permission_translations');
    }
}
