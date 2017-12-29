<?php

namespace app\modules\m0r1\controllers;

use Yii;

use yii\web\Controller;

class TestController extends Controller
{
    public function actionIndex()
    {
	//\app\m0r1\helpers\M0r1Bonuses::processSuccessBonuses(6);
	//\app\backend\models\BackendMenu::deleteAll(['like','name','M0r1 Finance System Backend Menu']);
	//Yii::trace(Yii::$app->getAuthManager()->getPermission('finance system manage'));
	
	//$perm = Yii::$app->getAuthManager()->getPermission('finance system manage');
	//Yii::$app->getAuthManager()->remove($perm);
	//Yii::$app->db->createCommand()->update(\app\m0r1\fs\models\M0r1FSTubeItem::tableName(),['pos'=>new \yii\db\Expression('pos - 1')],'tube_id = :tubeid AND pos > :pos',[':tubeid'=>1,':pos'=>1])->execute();
	
	//\app\m0r1\fs\helpers\StrgHlp::run( 1 );
	
	$price = (new \yii\db\Query())->select(new \yii\db\Expression( 'SUM(total_price)' ))->from(\app\modules\shop\models\OrderItem::tableName())->where(['order_id'=>1])->scalar();
	echo $price;
    }

}