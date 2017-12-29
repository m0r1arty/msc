<?php

namespace app\m0r1\behaviors;

use Yii;

use yii\base\Behavior;

use app\modules\m0r1\models\M0r1OrderBonuses;

use app\modules\shop\models\Currency;
use app\modules\shop\events\CartActionEvent;
use app\modules\shop\controllers\CartController;

class M0r1CartBonusesBehavior extends Behavior
{
    public static function m0r1CalculateCurrentBonuses(CartActionEvent $event)
    {
	$price = 0.0;
	
	foreach( $event->getOrder()->items as $item )
	{
	    $price += $item->total_price;
	}
	
	$currency = Currency::findOne( Yii::$app->getModule("m0r1")->bonusCurrencyID );
	
	if( !is_null( $currency ) )
	{
	    $order = $event->getOrder();
	    $order->bonuses = ($currency->convert_rate * $price);
	    $order->save();
	    
	    M0r1OrderBonuses::deleteAll(['order_id'=>$order->id]);
	}
    }
}