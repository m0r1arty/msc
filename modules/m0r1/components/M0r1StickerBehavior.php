<?php

namespace app\modules\m0r1\components;

use Yii;

use yii\base\Behavior;

use app\modules\shop\models\Order;

use app\modules\m0r1\models\M0r1Order2Sticker;

class M0r1StickerBehavior extends Behavior
{
    private $_sticker_id = 0;
    private $_inited = FALSE;
    
    public function init()
    {
	parent::init();
    }
    
    public function events()
    {
	return [
	    Order::EVENT_BEFORE_UPDATE => 'beforeO2SUpdate',
	];
    }
    
    public function beforeO2SUpdate( $event )
    {
	$own = $this->owner;
	$post = Yii::$app->request->post();
	$sid = intval( $post[ $own->formName() ]['sticker_id']  );
	
	if( $sid === 0 )
	{
	    M0r1Order2Sticker::deleteAll(['order_id' => $own->id]);
	}else{
	    $model = M0r1Order2Sticker::find()->where( ['order_id' => $own->id] )->one();
	    
	    if( is_null( $model ) )
	    {
		$model = new M0r1Order2Sticker();
		$model->order_id = $own->id;
	    }

	    $model->sticker_id = $sid;
	    
	    $model->save();
	}
    }
    
    public function getSticker_id()
    {
	if( !$this->_inited && !$this->owner->isNewRecord )
	{
	    
	    $model = M0r1Order2Sticker::find()->where( [ 'order_id' => $this->owner->id ] )->one();
	    
	    if( !is_null( $model ) )
	    {
		$this->_sticker_id = $model->sticker_id;
	    }
	    
	    $this->_inited = TRUE;
	}
	
	return $this->_sticker_id;
    }
}