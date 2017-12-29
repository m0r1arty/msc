<?php

namespace app\m0r1\fs\helpers;

use Yii;

use yii\base\InvalidConfigException;

use app\m0r1\fs\ProductBundleItem;
use app\m0r1\fs\ThingBundleItem;
use app\m0r1\fs\ProductThingItem;

use app\m0r1\fs\fakes\FakeOrder;

use app\m0r1\fs\models\M0r1FSConf;


use app\modules\shop\models\OrderItem;
use app\modules\shop\models\SpecialPriceObject;

class StrgHlp
{
    
    private static $pbundle = NULL;
    private static $tbundle = NULL;
    
    public static function getPBundle()
    {
	return static::$pbundle;
    }

    public static function getTBundle()
    {
	return static::$tbundle;
    }
    
    private static function getConf( $conf_id )
    {
	return M0r1FSConf::findOne( $conf_id );
    }
    
    public static function getDummyPrice( $product_array = [] )
    {
	if( count( $product_array ) > 0 )
	{
	    $m0r1 = Yii::$app->getModule('m0r1');
	    
	    $str = \app\m0r1\fs\models\M0r1FSStrategy::findOne( $m0r1->dummyStrategyID );
	    
	    if( is_null( $str ) )
		throw new InvalidConfigException;
	    
	    $pids = [];
	    
	    foreach( $product_array as $p )
	    {
		$p = intval( $p );
		
		if( $p === 0 )
		    continue;
		
		$pids[] = $p;
	    }
	    
	    $strategy = Yii::createObject([
		'class'	=> $str->class,
		'product_array'	=> $pids,
	    ],[]);
	    
	    $order = Yii::createObject([
		'class'		=> FakeOrder::className(),
	    ],[]);
	    
	    $order->setPIDs( $pids );
	    
	    $strategy->run( $order, $m0r1->magicTubeID );
	    
	    return $strategy->result;
	}
	
	return 0.0;
    }
    
    public static function RoundTo( $price, $to )
    {
	$count = floor( $price / $to );
	
	$remain = $price - ( $count * $to );
	
	if( $remain > ( $to / 2 ) )
	{
	    $count += 1;
	}
	
	return $count * $to;
    }
    
    public static function run( $order )
    {
	$m0r1 = Yii::$app->getModule('m0r1');
	
	$conf = static::getConf($m0r1->fsConfID);
	
	
	if( !is_null( $conf ) )
	{
	    if( !is_null( $conf->tube ) && !is_null( $conf->strategy ) )
	    {
		$strategy = Yii::createObject( $conf->strategy->class, [] );
		
		$strategy->run( $order, $conf->tube->id );
	    }
	}
    }
    
    public static function loadBundles( $order_id, \app\m0r1\fs\M0r1FSProductBundle $pbundle, \app\m0r1\fs\M0r1FSThingBundle $tbundle)
    {
	
	if( is_null( static::$pbundle ) )
	    static::$pbundle = $pbundle;

	if( is_null( static::$tbundle ) )
	    static::$tbundle = $tbundle;
	
	$allprice = 0;

	foreach( OrderItem::find()->where( [ 'order_id' => $order_id ] )->all() as $oi )
	{
	    $price = ( $oi->product->price * $oi->quantity );
	    $pbundle->addItem( Yii::createObject( [ 
		'class' => ProductBundleItem::className(), 
		'product_id' => $oi->product_id, 
		'qty' => $oi->quantity, 
		'price' =>  $price,
		'order_item' => $oi,
		'bundle' => $pbundle ,
	    ] )  );
	    
	    $allprice += $price;
	}
	
	$tbundle->addItem( 
	    Yii::createObject([
		'class' => ProductThingItem::className(),
		'price' => $allprice,
		'bundle'=> $tbundle,
		],[] 
	    )
	);
    }
    
    public static function getExTube( $tube_id )
    {
	return Yii::createObject([
	    'class' => \app\m0r1\fs\ExTube::className(),
	    'tube_id' => $tube_id,
	]);
    }
    
    public static function getProductPriceFromStorage( $key )
    {
	$price = 0.0;
	
	$strategy = Yii::$app->get('fsstrategy');
	
	foreach( $strategy->storage->getValue( $key ) as $p )
	{
	    $price += $p['price'];
	}
	
	return $price;
    }
    
    public static function removeDiscount( $order_id )
    {
	SpecialPriceObject::deleteAll( ['special_price_list_id' => 4, 'object_model_id' => $order_id ] );
    }
    
    public static function applyDiscount( $discount, $order, $dtid )
    {
	$oldPrice = $order->total_price;
	
	$price = $discount->getDiscountPrice( $order->total_price );
	$discountPrice = $price - $oldPrice;
	
	$model = SpecialPriceObject::find()->where(['special_price_list_id' => 4, 'object_model_id' => $order->id, 'discount_id' => $discount->id ])->one();
	
	if( is_null( $model ) )
	{
	    $model = new SpecialPriceObject();
	}
	
	$model->special_price_list_id = 4;
	$model->object_model_id = $order->id;
	$model->discount_id = $discount->id;
	$model->price = $discountPrice;
	$model->name = $discount->name;
	$model->save();
	
	return $price;
    }
}

