<?php

namespace app\modules\m0r1\widgets;

use Yii;

use yii\base\Widget;

use yii\base\InvalidConfigException;

use app\modules\m0r1\models\M0r1Order2Sticker;
use app\m0r1\widgets\ThumbnailWidget;

class OrderStickerView extends Widget
{
    public $order_id = 0;
    
    public function init()
    {
	if( $this->order_id === 0 )
	{
	    throw new InvalidConfigException();
	}
    }
    
    public function run()
    {
	
	$model = M0r1Order2Sticker::find()->where( [ 'order_id' => $this->order_id ] )->one();
	
	
	if( !is_null( $model ) )
	{
	    
	    return ThumbnailWidget::widget( [
		'img' 		=> $model->image,
		'sizeid'	=> Yii::$app->getModule('m0r1')->stickerThumbSizeID,
	    ] );
	    
	}else{
	    return '';
	}
    }
}