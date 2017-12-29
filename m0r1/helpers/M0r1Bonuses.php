<?php

namespace app\m0r1\helpers;

use Yii;

use yii\db\Query;
use yii\db\Expression;

use app\modules\m0r1\models\M0r1OrderBonuses;
use app\modules\m0r1\models\M0r1BonusesTransaction;

use app\modules\shop\models\Order;
use app\modules\shop\models\WarehouseProduct;

class M0r1Bonuses
{
    public static function getBPrice($order)
    {
	$bprice = 0;
	
	$model = (new Query())->select( new Expression('SUM(bonuses) as bonuses') )->from( M0r1OrderBonuses::tableName() )->where( ['order_id' => $order->id  ] )->one();
	
	if( !is_null( $model ) )
	{
	    $bprice += floatval( $model['bonuses'] );
	}
	
	$bprice = $order->bonuses - $bprice;
	
	if( !Yii::$app->user->isGuest )
	{
	    $bprice += Yii::$app->user->getIdentity()->bonuses;
	}
	
	return $bprice;
    }
    
    public static function processSuccessBonuses( $order_id )
    {
	if( !Yii::$app->user->isGuest )
	{
	    $model = M0r1BonusesTransaction::findOne( ['order_id' => $order_id,'user_id' => Yii::$app->user->getIdentity()->id] );
	    
	    if( is_null( $model ) )
	    {
		$user = Yii::$app->user->getIdentity();
		$order = Order::findOne($order_id);
		
		$bonuses = (new Query())->select( new Expression('product_id,qty,bonuses') )->from( M0r1OrderBonuses::tableName() )->where( ['order_id' => $order_id  ] )->all();
		
		if( !is_null($bonuses) && !is_null($order) )
		{
		    $transaction = Yii::$app->db->beginTransaction();
		    
		    $b = 0;

		    foreach( $bonuses as $bon )
		    {
			$whmodel = WarehouseProduct::findOne( ['product_id'=>$bon['product_id'] ] );
			
			if( !is_null( $whmodel ) )
			{
			    $whid = $whmodel->id;
			    
			    Yii::$app->db->createCommand('UPDATE {{%warehouse_product}} SET in_warehouse = in_warehouse - :remain WHERE id = :whid')->bindValues( [':remain'=>$bon['qty'], ':whid' => $whid ] )->execute();
			}
			$b += floatval( $bon['bonuses'] );
		    }
		    
		    $bprice = $order->bonuses - $b;
		    
		    $user->scenario = 'updateProfile';
		    
		    $user->bonuses += $bprice;
		    
		    if( $user->save() )
		    {
			$model = new M0r1BonusesTransaction();
			
			$model->attributes = [
			    'order_id' 	=> $order_id,
			    'user_id'	=> $user->id,
			    'bonuses'	=> $b,
			];
			
			if( $model->save() )
			{
			    $transaction->commit();
			}else{
			    $transaction->rollBack();
			}
			
		    }else{
			$transaction->rollBack();
		    }
		}
	    }
	}
    }
}