<?php

namespace app\m0r1\models;

use Yii;

use app\m0r1\models\M0r1ShareDiscountPids;

use yii\db\Expression;
use yii\helpers\ArrayHelper;
use app\modules\shop\models\AbstractDiscountType;
use app\modules\shop\models\Discount;
use app\modules\shop\models\Product;
use app\modules\shop\models\Order;

use app\m0r1\fs\M0r1FSDiscountInterface;

class M0r1ShareDiscount extends AbstractDiscountType implements M0r1FSDiscountInterface
{
    public static function tableName()
    {
	return '{{%m0r1_share_discount}}';
    }
    
    public function getFullName()
    {
	$count = count($this->products);
	
	return ( $count === 0 )? '(none)' : Yii::t('m0r1','M0r1 Share Discount Full Name{till}{count}',['till'=>$this->till,'count'=>$count]) ;
    }
    
    public function checkM0r1Discount($discount,$product = NULL, $order = NULL)
    {
	if( $discount == NULL || $product == NULL)
	{
	    return false;
	}
	
	$model = self::find()->where(['discount_id'=>$discount->id,'enabled'=>1])->andWhere( new Expression('till > NOW()') )->one();

	if( is_null($model) )
	    return false;
	
	$model = M0r1ShareDiscountPids::find()->where(['msid'=>$model->id,'pid'=>$product->id])->one();
	
	if( !is_null($model) )
	    return true;
	
	return false;
    }
    
    public function checkDiscount(Discount $discount,Product $product = NULL, Order $order = NULL)
    {
	if( $discount == NULL || $product == NULL)
	{
	    return false;
	}
	
	$model = self::find()->where(['discount_id'=>$discount->id,'enabled'=>1])->andWhere( new Expression('till > NOW()') )->one();

	if( is_null($model) )
	    return false;
	
	$model = M0r1ShareDiscountPids::find()->where(['msid'=>$model->id,'pid'=>$product->id])->one();
	
	if( !is_null($model) )
	    return true;
	
	return false;
    }
    
    public function getProducts()
    {
	$ret = [];

	foreach( $this->hasMany(M0r1ShareDiscountPids::className(),['msid'=>'id'])->all() as $pl )
	{
	    $ret = ArrayHelper::merge($ret,$pl->products);
	}

	return $ret;
    }
    
    /**
    * @inheritdoc
    */
    public function rules()
    {
	return [
	    [['till','discount_id'],'required'],
	    [['discount_id'],'integer'],
	    [['enabled'],'boolean'],
	];
    }
    
    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
	return [
	    'id' => Yii::t('app','ID'),
	    'till' => Yii::t('m0r1','Action till to'),
	    'enabled' => Yii::t('m0r1','Share Discount Enabled'),
	    'discount_id' => Yii::t('app','Discount ID'),
	];
    }
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
	M0r1ShareDiscountPids::deleteAll(['msid'=>$this->id]);
	parent::beforeDelete();
    }
}