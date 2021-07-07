<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductStockProductMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('product_meta', function (Blueprint $table) {
            $table->decimal('product_stock', 5, 0)->after('product_sale')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('product_meta', function (Blueprint $table) {
            if (Schema::hasColumn('product_meta', 'product_stock'))
            {
                $table->dropColumn('product_stock');
            }
        });
    }
}
