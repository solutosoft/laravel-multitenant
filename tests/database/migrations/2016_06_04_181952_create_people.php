<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeople extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        Schema::create('people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('firstName');
            $table->string('lastName');
            $table->dateTime('birthDate')->nullable(true);
            $table->decimal('salary', 18, 2)->nullable(true);
            $table->text('login');
            $table->string('password');
            $table->string('token');
            $table->boolean('super');
            $table->integer('tenant_id')->unsigned()->nullable(true);
        });
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        Schema::drop('people');
    }
}
