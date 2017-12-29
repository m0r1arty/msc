<?php

namespace app\m0r1\fs;

use Yii;

use yii\helpers\ArrayHelper;

abstract class AbstractThingItem extends AbstractBundleItem
{
    const PRODUCT_TYPE = 'product';
    
    protected $_type = '';
    public $price = 0.0;
    public $old_price = 0.0;
    public $bundle = NULL;
    
    public function getType()
    {
	return $this->_type;
    }
    
    public function setType( $type )
    {
	$this->_type = $type;
    }
    
    public function chPrice( $newprice, $msg, $msgp = [] )
    {
	$this->old_price = $this->price;
	$this->price = $newprice;
	
	Yii::warning( Yii::t('m0r1', $msg, ArrayHelper::merge( ['oldprice' => $this->old_price, 'newprice' => $this->price ], $msgp ) ), 'finance' );
    }
}