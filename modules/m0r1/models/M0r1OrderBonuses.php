<?php

namespace app\modules\m0r1\models;

use Yii;

use yii\db\ActiveRecord;

class M0r1OrderBonuses extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_order_bonuses}}';
    }
    
    public function rules()
    {
	return [
	    [['order_id','product_id','bonuses','qty'],'required'],
	    [['order_id','product_id','qty'],'integer'],
	    [['bonuses'],'number'],
	];
    }
    
    public function getProduct()
    {
	return $this->hasOne(\app\modules\shop\models\Product::className(),['id'=>'product_id']);
    }
    
    public function getOrder()
    {
	return $this->hasOne(\app\modules\shop\models\Order::className(),['id'=>'order_id']);
    }
}