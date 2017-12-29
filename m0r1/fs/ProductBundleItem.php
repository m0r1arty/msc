<?php

namespace app\m0r1\fs;

use Yii;

use yii\helpers\ArrayHelper;

use yii\base\InvalidConfigException;

use app\modules\shop\models\Product;

class ProductBundleItem extends AbstractBundleItem
{
    public $bundle = NULL;
    public $product_id = 0;
    public $product = null;
    public $qty = 0;
    public $price = 0.0;
    public $old_price = 0.0;
    public $order_item = NULL;
    
    public function init()
    {
	
	if( is_null( $this->bundle ) )
	{
	    throw new InvalidConfigException;
	}
	
	if( $this->product_id !== 0 )
	{
	    $this->product = Product::findOne($this->product_id);
	}
    }
    
    public function chPrice( $newprice, $msg, $msgp = [] )
    {
	$this->old_price = $this->price;
	$this->price = $newprice;
	
	Yii::warning( Yii::t('m0r1', $msg, ArrayHelper::merge( ['oldprice' => $this->old_price, 'newprice' => $newprice ], $msgp ) ), 'finance' );
    }
}