<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->text('short_des')->nullable();
            $table->string('logo')->nullable();
            $table->string('photo')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
            //$table->unsignedBigInteger('product_id')->index()->nullable();
            //$table->foreign('product_id')->references('product_id')->on('product_shop')->onDelete('SET NULL');
            $table->enum('status',['active','inactive'])->default('active');
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
        Schema::dropIfExists('shops');
    }
}
