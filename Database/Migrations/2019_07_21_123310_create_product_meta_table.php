<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_meta', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->unique();
            $table->decimal('product_price', 13,0);
            $table->decimal('product_sale', 13,0)->default(0);
            $table->timestamps();
        });

        Schema::table('product_meta', function($table){
            $table->foreign('product_id')->references('id_posts')->on('posts')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_meta');
    }
}
