<?php

namespace app\m0r1\fs\models;

use Yii;

use yii\db\ActiveRecord;

use app\m0r1\fs\models\M0r1FSItems;

class M0r1FSTubeItem extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_fs_tube_item}}';
    }
    
    public function rules()
    {
	return [
	    [['tube_id','item_id','pos','name'],'required'],
	    [['tube_id','item_id','pos'],'integer'],
	    [['params','name'],'string'],
	    [['name'],'string','max'=>255],
	];
    }
    
    public function getItem()
    {
	return $this->hasOne(M0r1FSItems::className(),[ 'id' => 'item_id' ] );
    }
}