<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table)
        {
            $table->increments('id');
            $table->enum('type', ['customer', 'consumer', 'admin', 'super']);
            $table->string('site_ids');
            $table->string('name');
            $table->string('email');
            $table->string('password');
            $table->string('telephone');
            $table->string('address_1');
            $table->string('address_2');
            $table->string('county');
            $table->string('city');
            $table->string('postcode');
            $table->unique('email');
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
        Schema::drop('users');
    }
}
