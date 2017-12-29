<?php

namespace app\m0r1\models;

use Yii;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use app\modules\shop\models\Product;

class M0r1ShareDiscountPids extends ActiveRecord
{

    public static function tableName()
    {
	return '{{%m0r1_share_discount_pids}}';
    }
    
    public function getProducts()
    {
	return $this->hasMany(Product::className(),['id'=>'pid'])->all();
    }

    /**
     * @inheritdoc
     */
     public function rules()
     {
        return [
    	    [['msid','pid'],'required'],
    	    [['msid','pid'],'integer']
        ];
     }

}