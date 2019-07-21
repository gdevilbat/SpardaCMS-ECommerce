<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductMeta extends Model
{
    protected $fillable = [];
    protected $table = 'product_meta';
    protected $primaryKey = 'product_id';

    /**
     * Set the Post Status.
     *
     * @param  string  $value
     * @return void
     */
    public function setProductSaleAttribute($value)
    {
        return false;
    }

    public static function getTableName()
    {
        return with(new Static)->getTable();
    }

    public static function getPrimaryKey()
    {
        return with(new Static)->getKeyName();
    }
}
