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
    
    public function setProductPriceAttribute($value)
    {
        $this->attributes['product_price'] = preg_replace('/[,_]/', '',$value);
    }
    
    public function setProductSaleAttribute($value)
    {
        if(empty($value))
        {
            $this->attributes['product_sale'] = 0;
        }
        else
        {
            $this->attributes['product_sale'] = preg_replace('/[,_]/', '',$value);
        }

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
