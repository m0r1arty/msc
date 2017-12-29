<?php

namespace app\m0r1\fs\models;


use Yii;

use yii\db\ActiveRecord;

class M0r1FSConds extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_fs_conds}}';
    }
    
    public function rules()
    {
	return [
	    [['name','class'],'required'],
	    [['name','class'],'string','max'=>255],
	];
    }
}