<?php

namespace app\m0r1\fs;

use Yii;

use yii\base\Component;

use app\m0r1\fs\AbstractBundleItem;

abstract class AbstractM0r1FSBundle extends Component
{
    protected $_items = [];
    protected $_strategy = NULL;
    
    public function addItem(AbstractBundleItem $item)
    {
	array_push( $this->_items, $item );
    }
    
    public function setStrategy( $strategy )
    {
	$this->_strategy = $strategy;
    }
    
    public function getStrategy()
    {
	return $this->_strategy;
    }
}