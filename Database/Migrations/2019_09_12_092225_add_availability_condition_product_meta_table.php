<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAvailabilityConditionProductMetaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_meta', function (Blueprint $table) {
            $table->enum('availability', ['in stock', 'out of stock', 'preorder', 'available for order', 'discontinued'])->after('product_sale')->default('in stock');
            $table->enum('condition', ['new', 'refurbished', 'used'])->after('product_sale')->default('new');
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
            if (Schema::hasColumn('product_meta', 'availability'));
            {
                $table->dropColumn('availability');
            }

            if (Schema::hasColumn('product_meta', 'condition'));
            {
                $table->dropColumn('condition');
            }
        });
    }
}
