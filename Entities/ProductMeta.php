<?php

namespace Gdevilbat\SpardaCMS\Modules\Ecommerce\Entities;

use Illuminate\Database\Eloquent\Model;

class ProductMeta extends Model
{
    protected $fillable = [];
    protected $table = 'product_meta';
    protected $primaryKey = 'product_id';

    CONST PRODUCT_VARIANT = "product_variant";

    CONST PRODUCT_WEIGHT = "product_weight";

    CONST SHOPEE_ATTR_COMBO = "COMBO_BOX";
    CONST SHOPEE_ATTR_DROPDOWN = "DROP_DOWN";
    CONST SHOPEE_ATTR_TEXT = "TEXT_FILED";

    CONST LAZADA_ATTR_COMBO = "multiSelect";
    CONST LAZADA_ATTR_DROPDOWN = "singleSelect";
    CONST LAZADA_ATTR_TEXT = "text";
    CONST LAZADA_ATTR_LONGTEXT = "richText";
    CONST LAZADA_ATTR_NUMERIC = "numeric";

    CONST TOKPED_SUPPLIER = 'tokopedia_supplier_account';
    CONST SHOPEE_SUPPLIER = 'shopee_supplier_account';
    CONST LAZADA_SUPPLIER = 'lazada_supplier_account';

    CONST TOKPED_STORE = 'tokopedia_store_account';
    CONST SHOPEE_STORE = 'shopee_store_account';
    CONST LAZADA_STORE = 'lazada_store_account';

    CONST STAT_IN_STOCK = 'in stock';
    CONST STAT_OUT_STOCK = 'out of stock';
    CONST STAT_PREORDER = 'preorder';
    CONST STAT_AVAILABLE = 'available for order';
    CONST STAT_DISCONTINUED = 'discontinued';

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
        if($this->availability == SELF::STAT_IN_STOCK)
            return 'http://schema.org/InStock';

        if($this->availability == SELF::STAT_OUT_STOCK)
            return 'http://schema.org/OutOfStock';

        if($this->availability == SELF::STAT_PREORDER)
            return 'http://schema.org/PreOrder';

        if($this->availability == SELF::STAT_AVAILABLE)
            return 'http://schema.org/LimitedAvailability';

        if($this->availability == SELF::STAT_DISCONTINUED)
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

    public function getAvailabilityAttribute($value)
    {
        if($value == SELF::STAT_PREORDER)
            return $value;

        if($value == SELF::STAT_AVAILABLE)
            return $value;

        if($value == SELF::STAT_DISCONTINUED)
            return $value;

        if($this->product_stock > 0)
            return SELF::STAT_IN_STOCK;

        return SELF::STAT_OUT_STOCK;
    }

    /**
     * Set the user's first name.
     *
     * @param  string  $value
     * @return void
     */
    public function setAvailabilityAttribute($value)
    {
        if($value == SELF::STAT_PREORDER)
            return $this->attributes['availability'] = $value;

        if($value == SELF::STAT_AVAILABLE)
            return $this->attributes['availability'] = $value;

        if($value == SELF::STAT_DISCONTINUED)
            return $this->attributes['availability'] = $value;

        if($this->product_stock > 0)
            return $this->attributes['availability'] = SELF::STAT_IN_STOCK;

        return $this->attributes['availability'] = SELF::STAT_OUT_STOCK;
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

    final static function getTableWithPrefix()
    {
        return with(new Static)->getConnection()->getTablePrefix().with(new Static)->getTable();
    }
}
