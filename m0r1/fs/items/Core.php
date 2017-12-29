<?php

namespace app\m0r1\fs\items;

use Yii;

use app\m0r1\fs\helpers\StrgHlp;

use app\modules\shop\models\Discount;
use app\modules\shop\models\DiscountType;

use app\modules\shop\helpers\PriceHelper;

use app\m0r1\fs\AbstractThingItem;
use app\m0r1\fs\M0r1FSProductBundle;
use app\m0r1\fs\M0r1FSThingBundle;

/**
    Этот класс содержит основные итемы
*/
class Core extends AbstractItem
{
    
/**
    Этот итем округляет прайс каждого товара до 50
*/
    public static function itemRoundProduct50( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle, $params = [] )
    {
	foreach( $pbundle->getProducts() as $pbi/*product bundle item*/ )
	{
	    $pbi->chPrice( StrgHlp::RoundTo($pbi->price,50) ,'M0r1 FS System Tube Log|Price|Core|RoundProduct50');
	}
    }

/**
    Этот итем округляет тотал заказа до 50
*/
    public static function itemRoundOrder50( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle, $params = [] )
    {
	$pitem = $tbundle->getItemByType( AbstractThingItem::PRODUCT_TYPE );
	$pitem->chPrice( StrgHlp::RoundTo($pitem->price,50), 'M0r1 FS System Tube Log|Price|Core|RoundProduct50' );
    }
/**
    Этот итем производит начальную конвертацию 
    из валюты товара в валюту сайта.
*/
    public static function itemCurrency( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle, $params = [] )
    {
	foreach( $pbundle->getProducts() as $pbi/*product bundle item*/ )
	{
	    $pbi->chPrice( PriceHelper::getProductPrice( $pbi->product, NULL, $pbi->qty, 'core' ), 'M0r1 FS System Tube Log|Price|Core|Currency' );
	}
    }

/**
    Этот итем позволяет применить накопительную скидку
*/
    public static function itemAccumulativeDiscount( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle, $params = [] )
    {
	$strategy = Yii::$app->get('fsstrategy');
	
	$order = $strategy->order;
	
	if( !is_null( $order->user ) && $order->user->madid > 0 )
	{
	    $discount = \app\m0r1\models\M0r1AccDiscount::findOne( [ 'id' => $order->user->madid ] );
	    
	    if( !is_null( $discount ) && !is_null( $discount->discount ) )
	    {
		$discount = $discount->discount;
		
		foreach( $pbundle->getProducts() as $p )
		{
		    $price = $discount->getDiscountPrice( $p->price );
		    $p->chPrice( $price, 'M0r1 FS System Tube Log|Price|Core|Discount', [ 'name' => $discount->name ] );
		}
	    }
	}
    }

/**
    Этот итем позволяет использовать существующую скидку
*/
    public static function itemDiscount( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle, $params = [] )
    {
	$dtid = intval( $params['disc_type_id'] );//type discount id
	$did  = intval( $params['disc_id'] ); // type discount id
	
	$discount = Discount::findOne( $did );
	
	$dt = DiscountType::findOne( $dtid );
	$dto = Yii::createObject( [
	    'class'	=> $dt->class,
	],[]);
	
	if( !is_null( $discount ) )
	{
	    switch( $discount->appliance )
	    {
		case 'products':
		    
		    $products = $pbundle->getProducts();
		    
		    foreach( $products as $p )
		    {
			if( $dto->checkM0r1Discount( $discount, $p->product, $pbundle->strategy->order ) )
			{
			    $price = $discount->getDiscountPrice( $p->price );
			    $p->chPrice( $price, 'M0r1 FS System Tube Log|Price|Core|Discount', [ 'name' => $discount->name ] );
			}
		    }
		    
		break;

		case 'order_with_delivery':
		case 'order_without_delivery':
		    
		    $order = $tbundle->strategy->order;
		    $pitem = $tbundle->getItemByType( AbstractThingItem::PRODUCT_TYPE );
		    
		    $flag = $dto->checkM0r1Discount( $discount, NULL, $order );
		    
		    if( $flag )
		    {
			$price = $discount->getDiscountPrice( $pitem->price );
			$tbundle->strategy->storage->setValue('discount',$discount);
			$tbundle->strategy->storage->setValue('discount_type_id',$dtid);
			$pitem->chPrice( $price, 'M0r1 FS System Tube Log|Price|Core|Discount', [ 'name' => $discount->name ] );
		    }
		    
		break;
	    }
	}
    }
    
    public static function viewDiscount( $params = [] )
    {
	
	$discounts = Discount::find()->orderBy(['id'=>SORT_ASC])->all();
	$discounttypes = DiscountType::find()->where(['active'=>1])->orderBy(['id'=>SORT_ASC])->all();
	
	return Yii::$app->controller->renderPartial('_part_item_discount',[ 'discounts' => $discounts, 'discounttypes' => $discounttypes, 'params' => $params ]);;
    }
}