<?php

namespace app\m0r1\behaviors;

use Yii;

use yii\base\Behavior;
use yii\base\ModelEvent;

use app\modules\m0r1\M0r1Module;

use app\m0r1\models\M0r1AccDiscount;

class M0r1AccDiscountBehavior extends Behavior
{
    public static function beforeUpdate( ModelEvent $event )
    {
	$model = $event->sender;
	
	if( $model->isAttributeChanged( 'order_stage_id' ) )
	{
	    $new_stage_id = intval( $model->getAttribute( 'order_stage_id' ) );
	    
	    $module = Yii::$app->getModule( 'm0r1' );
	    
	    if( $new_stage_id == $module->accDiscountStageId )
	    {
		$price = 0.0;
		
		switch( $module->accStrategy )
		{
		    case M0r1Module::ACC_STRATEGY_PRODUCT:
			
			$price = (new \yii\db\Query())->select( new \yii\db\Expression( 'SUM(total_price)' ) )->from( \app\modules\shop\models\OrderItem::tableName() )->where( ['order_id' => $model->id ] )->scalar();
			
		    break;
		    
		    case M0r1Module::ACC_STRATEGY_ORDER:
			
			$price = $model->total_price;
			
		    break;
		}
		
		Yii::$app->db->createCommand()->update( \app\modules\user\models\User::tableName(), ['mad_total' => new \yii\db\Expression('mad_total + :price')],'id = :userid',[':price' => $price,':userid' => $model->user_id ] )->execute();
		
		$total = $model->user->mad_total;
		$applydiscount = NULL;
		
		foreach( M0r1AccDiscount::find()->orderBy( ['price' => SORT_DESC ] )->all() as $disc )
		{
		    if( $total >= $disc->price )
		    {
			$applydiscount = $disc;
			break;
		    }
		}
		
		if( !is_null( $applydiscount ) )
		{
		    $model->user->madid = $applydiscount->id;
		    $model->user->scenario = 'admin';
		    $model->user->save();
		}
	    }
	}
    }
}