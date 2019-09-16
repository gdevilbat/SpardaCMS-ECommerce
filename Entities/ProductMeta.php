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
    
    public function getDiscountAttribute()
    {
        if($this->product_sale > 0)
            return round(($this->product_price - $this->product_sale)*100/$this->product_price, 2);

        return 0;
    }

    public function getSchemaAvailabilityAttribute()
    {
        if($this->availability == 'in stock')
            return 'http://schema.org/InStock';

        if($this->availability == 'out of stock')
            return 'http://schema.org/OutOfStock';

        if($this->availability == 'preorder')
            return 'http://schema.org/PreOrder';

        if($this->availability == 'available for order')
            return 'http://schema.org/LimitedAvailability';

        if($this->availability == 'discontinued')
            return 'http://schema.org/Discontinued';

        return 'http://schema.org/InStock';;
    }

    public function getSchemaConditionAttribute()
    {
        if($this->availability == 'new')
            return 'http://schema.org/NewCondition';

        if($this->availability == 'refurbished')
            return 'http://schema.org/RefurbishedCondition';

        if($this->availability == 'used')
            return 'http://schema.org/UsedCondition';

        if($this->availability == 'available for order')
            return 'http://schema.org/LimitedAvailability';

        return 'http://schema.org/NewCondition';;
    }
    
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

    public static function getTableColumns()
    {
        return with(new Static)->getConnection()->getSchemaBuilder()->getColumnListing(with(new Static)->getTable());
    }
}
