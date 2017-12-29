<?php

namespace app\m0r1\fs\conds;

use Yii;

use app\m0r1\fs\AbstractThingItem;

class Core extends AbstractCond
{
    public static function condTrue()
    {
	return true;
    }

    public static function condFalse()
    {
	return false;
    }
    
    public function valCondSaveProductPriceToStorage( $val )
    {
	$storagekey = $val;
	
	$strategy = Yii::$app->get('fsstrategy');
	
	$ret = [];
	
	foreach( $strategy->getPBundle()->getProducts() as $p )
	{
	    $ret[] = [
		'id' => $p->product->id,
		'price' => $p->price,
	    ];
	}
	
	$strategy->storage->setValue($storagekey,$ret);
	
	return false;
    }
    
    public function valCondSaveMinProductPriceToStorage( $val )
    {
	$storagekey = $val;
	
	$strategy = Yii::$app->get('fsstrategy');
	
	$ret = [];
	$ret_sum = floatval( 0.0 );
	
	foreach( $strategy->getPBundle()->getProducts() as $p )
	{
	    $ret_sum += $p->price;
	    
	    $ret[] = [
		'id' => $p->product->id,
		'price' => $p->price,
	    ];
	}
	
	$ret2 = $strategy->storage->getValue( $storagekey );
	$ret2_sum = floatval( 0.0 );
	
	foreach( $ret2 as $p )
	{
	    $ret2_sum += $p['price'];
	}
	
	if( $ret_sum < $ret2_sum )
	{
	    $strategy->storage->setValue( $storagekey, $ret );
	}
	
	return false;
    }

    public function valCondRestoreProductPriceFromStorage( $val )
    {
	$storagekey = $val;
	
	$strategy = Yii::$app->get('fsstrategy');
	
	$ret = $strategy->storage->getValue( $storagekey );
	
	foreach( $strategy->getPBundle()->getProducts() as $p )
	{
	    foreach( $ret as $r )
	    {
		if( $p->product->id == $r['id'] )
		{
		    $p->price = $r['price'];
		    break;
		}
	    }
	}
	
	return false;
    }
    
    public function valCondUpdateProductThingItemFromStorage($val)
    {
	
	$storagekey = $val;
	
	$strategy = Yii::$app->get('fsstrategy');
	
	$ret = $strategy->storage->getValue( $storagekey );
	
	$price = 0.0;
	
	foreach ( $ret as $p )
	{
	    $price += $p['price'];
	}
	
	$ptitem = $strategy->getTBundle()->getItemByType( AbstractThingItem::PRODUCT_TYPE );
	
	if( !is_null( $ptitem) )
	{
	    $ptitem->chPrice( $price, 'M0r1 FS System Tube Log|Price|Core|UpdateProductThingFromStorage', [ 'keyname' => $storagekey ] );
	}
	
	return false;
    }
    
    public function exprCondComparePriceThingItemProduct( $expr, $val )
    {
	$strategy = Yii::$app->get('fsstrategy');
	
	$ptprice = $strategy->getTBundle()->getItemByType(AbstractThingItem::PRODUCT_TYPE)->price;
	
	$ppprice = $strategy->storage->getValue( $val );
	
	switch( $expr )
	{
	    case "==":
		
		return $ptprice == $ppprice;
		
	    break;

	    case "<":
		
		return $ptprice < $ppprice;
		
	    break;

	    case "<=":
		
		return $ptprice <= $ppprice;
		
	    break;

	    case ">":
		
		return $ptprice > $ppprice;
		
	    break;

	    case ">=":
		
		return $ptprice >= $ppprice;
		
	    break;
	}
	return false;
    }
    
    public function valCondSetTypeP( $val )
    {
	$strategy = Yii::$app->get('fsstrategy');
	
	$strategy->storage->setValue( $val, "P" );
	
	return false;
    }

    public function valCondSetTypeT( $val )
    {
	$strategy = Yii::$app->get('fsstrategy');

	$strategy->storage->setValue( $val, "T" );
	
	return false;
    }
    
    
    public function valCondPriceArray2PriceFloat( $val )
    {
	$tmp = explode( ',', $val );
	
	$arrkey = trim( $tmp[0] );
	$floatkey = trim( $tmp[1] );
	
	$strategy = Yii::$app->get('fsstrategy');
	
	$ret = $strategy->storage->getValue( $arrkey );
	
	$price = 0.0;
	
	foreach ( $ret as $p )
	{
	    $price += $p['price'];
	}
	
	$strategy->storage->setValue( $floatkey, $price );
	
	return false;
    }
}

