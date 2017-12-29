<?php

namespace app\m0r1\fs\strategies;

use Yii;

use app\modules\shop\models\Product;

use app\m0r1\fs\helpers\StrgHlp;

use app\m0r1\fs\ProductBundleItem;
use app\m0r1\fs\ProductThingItem;

use app\m0r1\fs\M0r1FSProductBundle;
use app\m0r1\fs\M0r1FSThingBundle;

use app\m0r1\fs\AbstractM0r1FSStrategy;

class Dummy extends AbstractM0r1FSStrategy implements \app\m0r1\fs\StrategyInterface
{
    public $pBundle = NULL;
    public $tBundle = NULL;
    
    public $product_array = [];
    
    private $_result = 0.0;
    
    public function getResult()
    {
	return $this->_result;
    }
    
    public function getPBundle()
    {
	return $this->pBundle;
    }
    
    public function getTBundle()
    {
	return $this->tBundle;
    }
    
    public function init()
    {
	parent::init();
	
	Yii::$app->set('fsstrategy',$this);
    }
    
    public function loadItems()
    {
	if( !is_null( $this->pBundle ) && !is_null( $this->tBundle ) )
	{
	    $price = 0.0;
	    
	    foreach( $this->product_array as $pid )
	    {
		$product = Product::findOne( $pid );
		
		if( !is_null( $product ) )
		{
		    $price += $product->price;
		    
		    $this->pBundle->addItem( Yii::createObject( [
			'class'		=> ProductBundleItem::className(),
			'product_id'	=> $pid,
			'qty'		=> 1,
			'price'		=> $product->price,
			'order_item'	=> NULL,
			'bundle'	=> $this->pBundle,
		    ],[]));
		}
	    }
	    
	    $this->tBundle->addItem( Yii::createObject( [
		    'class'	=> ProductThingItem::className(),
		    'price'	=> $price,
		    'bundle'	=> $this->tBundle,
	    ],[]));
	}
    }
    
    public function run( $order, $tube_id)
    {
	$pbundle = Yii::createObject([
	    'class'	=> M0r1FSProductBundle::className(),
	    'strategy'	=> $this,
	], []);
	$tbundle = Yii::createObject([
	    'class'	=> M0r1FSThingBundle::className(),
	    'strategy'	=> $this,
	], []);
	
	$this->pBundle = $pbundle;
	$this->tBundle = $tbundle;
	
	$this->order = $order;
	
	$this->loadItems();
	
	$tube = StrgHlp::getExTube( $tube_id );
	
	while( !is_null( $tube = $tube->step( $pbundle, $tbundle ) ) )
	{
	    //
	}
	
	$typediscount = $this->storage->getValue( 'price_type' );
	
	if ( !is_null( $typediscount ) )
	{
	    if( $typediscount === "P" )
	    {
		list( $price ) = $this->storage->getValue( 'price_min' );
		
		$this->_result = $price['price'];
		
	    }else if( $typediscount === "T" ){
	    
		$this->_result = $this->storage->getValue('price_float');
		
	    }
	}
    }
}
















