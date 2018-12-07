<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('tid')->index()->unique();
            $table->integer('reference')->index();
            $table->string('contact', 255);
            $table->string('mobile', 11)->nullable();
            $table->string('policy_number', 255)->index();
            $table->double('collection_amount', 10, 2);
            $table->tinyInteger('synced')->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('collections');
    }
}
