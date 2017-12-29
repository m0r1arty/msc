<?php

namespace app\m0r1\components;

use app\modules\shop\events\OrderCalculateEvent;
use app\modules\shop\models\AbstractDiscountType;
use app\modules\shop\models\Currency;
use app\modules\shop\models\Discount;
use app\modules\shop\models\Order;
use app\modules\shop\models\Product;
use app\modules\shop\models\SpecialPriceList;
use app\modules\shop\models\SpecialPriceObject;
use Yii;

class M0r1DiscountsEventHandler
{
    /**
     * @var array|null
     */
    static protected $discountsByAppliance = null;
    
    /**
     * @param array $types
     * @return array
     */
    static protected function getDiscountsByAppliance($types = [])
    {
	if (true === empty($types)) {
	    return [];
	}
	
	if (null === static::$discountsByAppliance) {
	    static::$discountsByAppliance = [];
	    foreach (Discount::find()->all() as $model) {
		/** @var Discount $model */
		static::$discountsByAppliance[$model->appliance][$model->id] = $model;
	    }
	}
	
	$result = [];
	
	foreach ($types as $type) {
	    if (true === isset(static::$discountsByAppliance[$type])) {
		$result = array_merge($result, static::$discountsByAppliance[$type]);
	    }
	}
	
	return $result;
    }
    
    /**
     * @param OrderCalculateEvent $event
     * @return null
     */
    
    static public function handleSaveDiscounts(OrderCalculateEvent $event)
    {
	return null;
	if (OrderCalculateEvent::BEFORE_CALCULATE !== $event->state) {
	    return null;
	}
	
	static $discounts = null;
	
	if (null === $discounts) {
	    $discounts = self::getDiscountsByAppliance(['order_without_delivery', 'order_with_delivery', 'delivery']);
	}
	
	foreach ($discounts as $discount) {
	    /** @var Discount $discount */
	    $discountFlag = 0;
	    foreach (Discount::getTypeObjects() as $discountTypeObject) {
		/** @var AbstractDiscountType $discountTypeObject */
		if (true === $discountTypeObject->checkDiscount($discount, null, $event->order)) {
		    $discountFlag++;
		}
	    }

	    $special_price_list_id = SpecialPriceList::find()
		->where([
		    'handler' => 'getDiscountPriceOrder',
		    'object_id' => $event->order->object->id
		])->one()->id;
	    
	    if ($discountFlag > 0 && $event->price > 0 && (
		$discount->apply_order_price_lg !== -1 && $event->order->total_price > $discount->apply_order_price_lg
		)
	    ) {
		$oldPrice = $event->price;
		$deliveryPrice = SpecialPriceObject::getSumPrice( $event->order->id, SpecialPriceList::TYPE_DELIVERY );
		
		$price = $discount->getDiscountPrice($oldPrice, $deliveryPrice);
		$discountPrice = $price - $oldPrice;
		
		$model = SpecialPriceObject::find()->where(['special_price_list_id'=>$special_price_list_id,'object_model_id'=>$event->order->id,'discount_id'=>$discount->id])->one();
		
		if( $model === null )
		{
		    $model = new SpecialPriceObject();
		}
		$model->special_price_list_id = $special_price_list_id;
		$model->object_model_id = $event->order->id;
		$model->discount_id = $discount->id;
		$model->price = $discountPrice;
		$model->name = $discount->name;
		$model->save();
		//SpecialPriceObject::setObject($special_price_list_id,$event->order->id,$discountPrice,$discount->name);
	    }else{
		$model = SpecialPriceObject::find()->where(['special_price_list_id'=>$special_price_list_id,'object_model_id'=>$event->order->id,'discount_id'=>$discount->id])->one();
		
		if( $model !== null)
		{
		    $model->delete();
		}
	    }
	}
    }
}