<?php

namespace app\modules\m0r1\models;

use yii\db\ActiveRecord;

class M0r1BonusesTransaction extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_bonuses_transaction}}';
    }
    
    public function rules()
    {
	return [
	    [['order_id','user_id','bonuses'],'required'],
	    [['order_id','user_id'],'integer'],
	    [['bonuses'],'number'],
	];
    }
}