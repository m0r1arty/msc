<?php

namespace app\m0r1\fs\strategies;

use Yii;

use app\m0r1\fs\helpers\StrgHlp;
use app\m0r1\fs\M0r1FSProductBundle;
use app\m0r1\fs\M0r1FSThingBundle;
use app\m0r1\fs\AbstractM0r1FSStrategy;
use app\m0r1\fs\AbstractThingItem;

use app\modules\shop\models\Order;

class M0r1PBD extends AbstractM0r1FSStrategy implements \app\m0r1\fs\StrategyInterface
{
    protected $pBundle = NULL;
    protected $tBundle = NULL;
    
    public function init()
    {
	parent::init();

	Yii::$app->set('fsstrategy',$this);
    }
    
    public function getPBundle()
    {
	return $this->pBundle;
    }

    public function getTBundle()
    {
	return $this->tBundle;
    }
    
    public function run( $order, $tube_id )
    {
	$pbundle = Yii::createObject( [
	    'class'	=> M0r1FSProductBundle::className(),
	    'strategy'	=> $this,
	], [] );

	$tbundle = Yii::createObject( [
	    'class'	=> M0r1FSThingBundle::className(),
	    'strategy'	=> $this,
	], [] );
	
	$this->pBundle = $pbundle;
	$this->tBundle = $tbundle;
	
	$this->order = $order;
	
	StrgHlp::loadBundles( $order->id, $pbundle, $tbundle );
	
	$tube = StrgHlp::getExTube( $tube_id );
	
	while( !is_null( $tube = $tube->step( $pbundle, $tbundle ) ) )
	{
	    //
	}
	
	$typediscount = $this->storage->getValue( 'price_type' );
	
	if( !is_null( $typediscount ) )
	{
	    if( $typediscount === "P" )
	    {
		$tprice = 0;
		
		foreach( $pbundle->getProducts() as $p )
		{
		    foreach( $this->storage->getValue('price_min') as $pp )
		    {
			if( $p->product->id === $pp['id'] )
			{
			    $tprice += $pp['price'];
			    
			    $p->order_item->total_price = $pp['price'];
			    $p->order_item->save();
			    break;
			}
		    }
		}
		
		$this->order->total_price = $tprice;
		
	    }else if( $typediscount === "T" )
	    {
		$discount = $this->storage->getValue('discount');
		$dtid = $this->storage->getValue('discount_type_id');
		
		$price = StrgHlp::getProductPriceFromStorage('price');
		
		$pitem = $tbundle->getItemByType(AbstractThingItem::PRODUCT_TYPE);
		
		$pitem->price = $price;
		
		$this->order->total_price = $price;
		
		$new_price = StrgHlp::applyDiscount( $discount, $this->order, $dtid );
		
		$this->order->total_price = $new_price;
	    }
	}
	
	if( is_null( $typediscount ) || $typediscount === "P" )
	{
	    StrgHlp::removeDiscount( $order->id );
	}
    }
}
