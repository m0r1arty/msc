<?php

namespace app\modules\m0r1\widgets;

use Yii;

use yii\base\Widget;

use yii\base\InvalidConfigException;

class OrderSticker extends Widget
{
    public $order = NULL;
    public $form  = NULL;
    
    public function init()
    {
	parent::init();
	
	if( is_null( $this->order ) || is_null( $this->form ) )
	{
	    throw new InvalidConfiException;
	}
    }
    
    public function run()
    {
	return $this->render( 'ordersticker', ['model' => $this->order, 'form' => $this->form ] );
    }
}