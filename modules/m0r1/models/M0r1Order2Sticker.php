<?php

namespace app\modules\m0r1\models;

use Yii;

use yii\db\ActiveRecord;

use app\modules\image\models\Image;

class M0r1Order2Sticker extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_order2sticker}}';
    }
    
    public function rules()
    {
	return [
	    [['order_id','sticker_id'],'required'],/*sticker_id - id from {{%image}}*/
	    [['order_id','sticker_id'],'integer'],
	];
    }
    
    public static function getStickers()
    {
	$ret = [];
	
	foreach( Image::find()->where( [ 'object_id' => 4/*Order*/, 'object_model_id' => 0/*static special value for stickers*/ ] )->all() as $o2s)
	{
	    $ret[] = [ 'id' => $o2s->id, 'value' => $o2s->image_title ];
	}
	
	return $ret;
    }
    
    public function getImage()
    {
	return $this->hasOne(\app\modules\image\models\Image::className(),['id' => 'sticker_id']);
    }
}
