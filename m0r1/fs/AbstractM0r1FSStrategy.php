<?php

namespace app\m0r1\fs;

use Yii;

use yii\base\Component;

use app\m0r1\fs\M0r1FSStorage;

abstract class AbstractM0r1FSStrategy extends Component
{
    protected $_order = NULL;
    
    public $storage = NULL;
    
    public function init()
    {
	parent::init();
	
	$this->storage = Yii::createObject([
	    'class' => M0r1FSStorage::className(),
	],[]);
    }
    
    public function setOrder( $order )
    {
	$this->_order = $order;
    }
    
    public function getOrder()
    {
	return $this->_order;
    }
}