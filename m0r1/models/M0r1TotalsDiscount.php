<?php

namespace app\m0r1\models;

use Yii;

use \app\modules\shop\models\AbstractDiscountType;
use \app\modules\shop\models\Discount;
use \app\modules\shop\models\Product;
use \app\modules\shop\models\Order;

use app\m0r1\fs\AbstractThingItem;

use app\m0r1\fs\M0r1FSDiscountInterface;

class M0r1TotalsDiscount extends AbstractDiscountType implements M0r1FSDiscountInterface
{
    public static function tableName()
    {
	return '{{%m0r1_totals_discount}}';
    }
    
    public function getFullName()
    {
	return $this->total;
    }
    
    public function checkM0r1Discount( $discount, $product = NULL, $order = NULL )
    {
	if( $order == null || $discount == null )
	{
	    return false;
	}

	$model = self::find()->where(['discount_id'=>$discount->id])->one();
	
	if( $model === null )
	{
	    return false;
	}
	
	$strg = Yii::$app->get('fsstrategy', false);
	
	if( !is_null( $strg ) )
	{
	    
	    $price = $strg->getTBundle()->getItemByType( AbstractThingItem::PRODUCT_TYPE )->price;
	    
	    if( $price >= $model->total )
		return true;
	    
	    return false;
	    
	}else{
	    $price = 0;
	    
	    foreach($order->items as $item)
	    {
		$price += $item->total_price;
	    }
	
	    if( $price >= $model->total )
		return true;
	}
	
	return false;
    }

    public function checkDiscount(Discount $discount, Product $product = null, Order $order = null)
    {
	if( $order == null || $discount == null )
	{
	    return false;
	}

	$model = self::find()->where(['discount_id'=>$discount->id])->one();
	
	if( $model === null )
	{
	    return false;
	}
	
	$strg = Yii::$app->get('fsstrategy', false);
	
	if( !is_null( $strg ) )
	{
	    
	    $price = $strg->getTBundle()->getItemByType( AbstractThingItem::PRODUCT_TYPE )->price;
	    
	    if( $price >= $model->total )
		return true;
	    
	    return false;
	    
	}else{
	    $price = 0;
	    
	    foreach($order->items as $item)
	    {
		$price += $item->total_price;
	    }
	
	    if( $price >= $model->total )
		return true;
	}
	
	return false;
    }

    /**
    * @inheritdoc
    */
    
    public function rules()
    {
	return [
	    [['total', 'discount_id'], 'required'],
	    [['discount_id'], 'integer'],
	    [['total'], 'number']
	];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
	return [
    	    'id' => Yii::t('app', 'ID'),
            'total' => Yii::t('app', 'Total'),
            'discount_id' => Yii::t('app', 'Discount ID'),
	];
    }
}