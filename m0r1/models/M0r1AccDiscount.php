<?php

namespace app\m0r1\models;

use Yii;

use yii\helpers\ArrayHelper;

use app\modules\shop\models\AbstractDiscountType;
use app\modules\shop\models\Product;
use app\modules\shop\models\Order;
use app\modules\shop\models\Discount;
use app\modules\shop\models\Currency;
use app\modules\shop\helpers\CurrencyHelper;

use app\m0r1\fs\M0r1FSDiscountInterface implements M0r1FSDiscountInterface

class M0r1AccDiscount extends AbstractDiscountType
{
    public static function tableName()
    {
	return '{{%m0r1_accumulative_discount}}';
    }
    
    public function getFullName()
    {
	$cur = CurrencyHelper::getMainCurrency();
	return $cur->format( $this->price );
    }
    
    public function checkM0r1Discount( $discount, $product = NULL, $order = NULL )
    {
	return false;
    }
    
    public function checkDiscount( Discount $discount, Product $product = NULL, Order $order = NULL )
    {
	return false;
    }
    
    public function getAsArray()
    {
	$cur = CurrencyHelper::getMainCurrency();
	
	$ret = [];
	
	foreach( M0r1AccDiscount::find()->orderBy( [ 'price' => SORT_ASC ] )->all() as $ad )
	{
	    $disc = $ad->discount;
	    $ret[] = [ 'id' => $ad->id, 'value' => Yii::t( 'm0r1','M0r1 Accumulative Discount Format Template',[ 'price' => $cur->format($ad->price), 'discount' => $disc->value.(($disc->value_in_percent)?'%':'') ]) ];
	}
	return ArrayHelper::map( $ret, 'id', 'value' );
    }
    
    public function getDiscount()
    {
	return $this->hasOne( Discount::className(), ['id'=>'discount_id'] );
    }
    
    /**
    * @inheritdoc
    */
    public function rules()
    {
	return [
	    [['price','discount_id'],'required'],
	    [['price'],'number'],
	    [['discount_id'],'integer'],
	];
    }
    
    public function attributeLabels()
    {
	return [
	    'price'	=> Yii::t('m0r1','M0r1 Accumulative Discount Title Price'),
	];
    }
    
}