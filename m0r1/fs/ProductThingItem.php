<?php

namespace app\m0r1\fs;

use Yii;

class ProductThingItem extends AbstractThingItem
{
    
    public function init()
    {
	parent::init();
	
	$this->type = self::PRODUCT_TYPE;
    }
}
