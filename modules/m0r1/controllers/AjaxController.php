<?php

namespace app\modules\m0r1\controllers;

use Yii;

use app\m0r1\models\M0r1ShareDiscount;
use app\m0r1\models\M0r1ShareDiscountPids;
use app\m0r1\helpers\M0r1Bonuses;

use app\modules\m0r1\models\M0r1OrderBonuses;
use app\modules\shop\models\Product;
use app\modules\shop\models\Currency;
use app\modules\shop\models\WarehouseProduct;

use yii\db\Expression;

use yii\web\Controller;
use yii\web\Response;

use yii\helpers\Json;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\filters\AccessControl;

use yii\base\InvalidParamException;
use yii\base\InvalidValueException;
use yii\web\NotFoundHttpException;

class AjaxController extends Controller
{
    
    const ESC_DUMMY	= 0;
    const ESC_CHTOTAL 	= 1;
    
    public function behaviors()
    {
	return [
	    'access' => [
		'class' => AccessControl::className(),
		'rules' => [
		    [
			'allow' => true,
			'roles' => ['admin'],
		    ],
		],
	    ],
	];
    }
    
    public function actionGetsharediscount($id = 0)
    {
	if( ! Yii::$app->request->isAjax )
	    throw new NotFoundHttpException;
	
	$id = intval($id);
	
	$model = M0r1ShareDiscount::findOne($id);
	
	if( is_null($model) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Not Found'),
	    ]);
	}
	
	$products = [];
	
	foreach( $model->products as $p )
	{
	    $products[] = [
		'id' => $p->id,
		'name' => $p->name,
		'price' => $p->price,
		'price_f' => $p->currency->format($p->price),
	    ];
	}
	
	return Json::encode([
	    'status' => 1,
	    'till' => $model->till,
	    'enabled' => $model->enabled,
	    'products' => $products,
	]);
    }
    
    public function actionSavesharediscount($id = 0)
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$id = intval($id);
	
	$model = M0r1ShareDiscount::findOne($id);
	
	if( is_null($model) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Not Found'),
	    ]);
	}
	
	$model->enabled = Yii::$app->request->post('enabled');
	$model->till = Yii::$app->request->post('till');
	
	if( ! $model->validate() )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => $model->errors[0],
	    ]);
	}
	
	if( $model->save() )
	{
	
	    return Json::encode([
		'status' => '1',
		'reason' => Yii::t('m0r1','M0r1 Share Discount Was Saved'),
		'type' => 'success',
	    ]);
	}else{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','M0r1 Share Discount Save Failed'),
	    ]);
	}
    }
    
    public function actionPunlink($id = 0)
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$id = intval($id);
	
	$model = M0r1ShareDiscount::findOne($id);
	
	if( is_null($model) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Not Found'),
	    ]);
	}
	
	$pid = intval( Yii::$app->request->post('pid') );
	
	$model = M0r1ShareDiscountPids::find()->where(['msid'=>$id,'pid'=>$pid])->one();
	
	if( is_null( $model ) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Plink Not Found'),
	    ]);
	}
	
	if( $model->delete() !== FALSE )
	{
	    return Json::encode([
		'status' => 1,
		'type' => 'success',
		'pid' => $pid,
		'reason' => Yii::t('m0r1','Share Discount Plink Was Successfully Deleted'),
	    ]);
	}else{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Plink Was Unsuccessfully Deleted:{error}',['error' => $this->errors[0] ]),
	    ]);
	}
    }
    
    public function actionCreateplink($id = 0)
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$id = intval($id);
	
	$model = M0r1ShareDiscount::findOne($id);
	
	if( is_null($model) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','Share Discount Not Found'),
	    ]);
	}
	
	$pid = intval( Yii::$app->request->post('pid') );
	
	$model = M0r1ShareDiscountPids::find()->where(['msid'=>$id,'pid'=>$pid])->one();
	
	if( !is_null( $model ) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','M0r1 Share Discount Plink Already Exists'),
	    ]);
	}

	$pmodel = Product::findOne($pid);
	
	if( is_null($pmodel) )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','M0r1 Share Discount Plink Product Not Found'),
	    ]);
	}

	$model = new M0r1ShareDiscountPids();
	$model->attributes = [
	    'msid' => $id,
	    'pid'  => $pid,
	];
	
	if( !$model->save() )
	{
	    return Json::encode([
		'status' => 0,
		'reason' => Yii::t('m0r1','M0r1 Share Discount Plink Create Failed:{error}',['error'=>$this->errors[0]]),
	    ]);
	}
	
	return Json::encode([
	    'status' => 1,
	    'reason' => Yii::t('m0r1','M0r1 Share Discount Plink Create Successfully'),
	    'type' => 'success',
	    'product' => [
		'id' => $pmodel->id,
		'name' => $pmodel->name,
		'price' => $pmodel->price,
		'price_f' => $pmodel->currency->format($pmodel->price),
	    ],
	]);
    }
    
    public function actionBshop()
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$bid = intval( Yii::$app->request->post( 'id', '0' ) );
	
	$count = intval( Yii::$app->request->post( 'count' , '0' ) );
	
	if( $bid == 0 || $count == 0 )
	    throw new InvalidParamException;
	
	$post = Yii::$app->request->post();
	
	if( isset( $post['order_id'] ) && Yii::$app->user->can('admin') )
	{
	    $isAdmin = true;
	    $order = \app\modules\shop\models\Order::findOne( intval( $post['order_id'] ) );
	}else{
	    $isAdmin = false;
	    $order = \app\modules\shop\models\Order::getOrder(false);
	}

	if ( is_null( $order ) )
	    throw new InvalidValueException;
	
	$bonuses = Currency::findOne( Yii::$app->getModule('m0r1')->bonusCurrencyID );
	
	if ( is_null( $bonuses ) )
	    throw new InvalidValueException;
	
	$ret = [];
	
	$total_count = intval( array_reduce( WarehouseProduct::findAll( ['product_id'=>$bid] ), function($old,$item){
	    return $old + $item->in_warehouse;
	},0 ) );
	
	$model = Product::findOne( $bid );
	
	if( is_null( $model ) )
	{
	    if( !isset( $ret['error'] ) )
		$ret['error'] = [];
	
	    $ret['error'][] = ['msg' => Yii::t('m0r1','M0r1BBW Bonuses Error Product Not Found'), 'subcode' => self::ESC_DUMMY  ];
	}
	
	if( $total_count < $count )
	{
	    if( !isset( $ret['error'] ) )
		$ret['error'] = [];
	    
	    $ret['error'][] = ['msg' => Yii::t( 'm0r1','M0r1BBW Bonuses Error Count Too Many',[ 'total'=> $total_count ] ), 'subcode' => self::ESC_CHTOTAL, 'total' => $total_count, 'id' => $bid ];
	}
	
	if( !isset( $ret['error'] ) )
	{
	    $product = $model;
	    
	    $model = M0r1OrderBonuses::findOne( ['order_id'=>$order->id,'product_id'=>$bid] );
	
	    if( is_null( $model ) )
	    {
		$oldcount = 0;
		$model = new M0r1OrderBonuses();
		
		$model->setAttributes([
		    'order_id'		=> $order->id,
		    'product_id'	=> $bid,
		    'qty'		=> $count,
		    'bonuses'		=> ($product->price * $count),
		]);
		
	    }else{
		$oldcount = $model->qty;
		$model->qty = $count;
		$model->bonuses = ($count * $product->price);
	    }
	    
	    if( $model->save() )
	    {
		$ret['success'] = ['msg' => Yii::t('m0r1','M0r1BBW Bonuses Error Success',['count' => $count,'name'=>Html::encode($product->name)]), 'id' => $bid ];
		
		if( $isAdmin )
		{
		    $whmodel = WarehouseProduct::findOne(['product_id' => $bid]);
		    
		    $whid = $whmodel->id;
		    
		    if( $oldcount > $count )//уменьшили у пользователя - на склад добавили
		    {
			Yii::$app->db->createCommand("UPDATE {{%warehouse_product}} SET in_warehouse = in_warehouse + :remain WHERE id = :whid")->bindValues([':remain' => ($oldcount - $count), ':whid' => $whid])->execute();
		    }else{//добавили пользователю - надо вычесть со склада
			Yii::$app->db->createCommand("UPDATE {{%warehouse_product}} SET in_warehouse = in_warehouse - :remain WHERE id = :whid")->bindValues([':remain' => ($count - $oldcount), ':whid' => $whid])->execute();
		    }
		    
		    $product->invalidateTags();
		    
		}
		
	    }else{
		if( !isset( $ret['error'] ) )
		    $ret['error'] = [];
		
		$ret['error'][] = ['msg' => $model->errors[0], 'subcode' => ESC_DUMMY ];
	    }
	}
	
	$ret['button'] = Yii::t( 'm0r1','M0r1BBW {countbonuses} bonuses',['countbonuses' => $isAdmin ? 'XXXXXX' : $bonuses->format( M0r1Bonuses::getBPrice($order) ) ] );
	
	return Json::encode($ret);
    }
    
    public function actionBunshop()
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$id = intval( Yii::$app->request->post('id','0') );
	
	if ( $id <= 0 )
	    throw new InvalidParamException;

	$post = Yii::$app->request->post();
	
	if( isset( $post['order_id'] ) && Yii::$app->user->can('admin') )
	{
	    $isAdmin = true;
	    $order = \app\modules\shop\models\Order::findOne( intval( $post['order_id'] ) );
	}else{
	    $isAdmin = false;
	    $order = \app\modules\shop\models\Order::getOrder(false);
	}

	if ( is_null( $order ) )
	    throw new InvalidValueException;

	$bonuses = Currency::findOne( Yii::$app->getModule('m0r1')->bonusCurrencyID );
	
	if ( is_null( $bonuses ) )
	    throw new InvalidValueException;
	
	$ret = [ 'id' => $id ];
	
	$model = M0r1OrderBonuses::find()->where([ 'order_id' => $order->id, 'product_id' => $id  ])->one();
	
	if( is_null( $model ) )
	{
	    if(! isset( $ret['warning'] ) )
	    {
		$ret['warning'] = [];
	    }
	    
	    $ret['warning'][] = ['msg' => Yii::t('m0r1','M0r1BBW Bonuses Error Delete Bonus Not Found',['id' => $id]), ];
	}
	
	if ( $isAdmin )
	{
	    $count = $model->qty;
	}
	
	if ( !is_null( $model ) && ( $cdeleted = $model->delete() ) === FALSE )
	{
	    if( !isset( $ret['error'] ) )
		$ret['error'] = [];
	    
	    $ret['error'][] = [ 'msg' => $model->errors[0] ];
	}
	
	if( is_null( $model ) || $cdeleted !== FALSE )
	{
	    $ret['msg'] = Yii::t('m0r1','M0r1BBW Bonuses Error Success Deleted');
	    
	    if( $isAdmin )
	    {
		$whmodel = WarehouseProduct::findOne( [ 'product_id' => $id ] );
		
		if( !is_null( $whmodel ) )
		{
		    $whid = $whmodel->id;
		    
		    Yii::$app->db->createCommand( "UPDATE {{%warehouse_product}} SET in_warehouse = in_warehouse + :remain WHERE id = :whid" )->bindValues( [ ':remain' => $count, ':whid' => $whid ] )->execute();
		    
		    if( !is_null( ( $product = Product::findOne( $id ) ) ) )
		    {
			$product->invalidateTags();
		    }
		}
	    }
	}
	
	$ret['button'] = Yii::t( 'm0r1','M0r1BBW {countbonuses} bonuses',['countbonuses' => $isAdmin?'XXXXXX':$bonuses->format( M0r1Bonuses::getBPrice($order) ) ] );
	
	return Json::encode($ret);
    }
    
    public function actionGetbonuspage()
    {
	if( ! Yii::$app->request->isAjax || ! Yii::$app->request->isPost )
	    throw new NotFoundHttpException;
	
	$page = intval(Yii::$app->request->post('page','0')) - 1;
	
	if( $page < 0 )
	    throw new InvalidParamException;
	    
	$post = Yii::$app->request->post();

	$order = ( isset( $post['order_id'] ) && Yii::$app->user->can('admin') )?\app\modules\shop\models\Order::findOne( intval( $post['order_id'] ) ):\app\modules\shop\models\Order::getOrder(false);

	if ( is_null( $order ) )
	    throw new InvalidValueException;


	$m0r1 = Yii::$app->getModule('m0r1');
	
	$currency = Currency::findOne( $m0r1->bonusCurrencyID );
	
	if ( is_null( $currency ) )
	    throw new NotFoundHttpException;
	
	$count = Product::find()->where(['main_category_id' => $m0r1->bonusCategoryID, 'active' => 1 ])->count();
	$countPage = ( ( $count % $m0r1->bonusesPerPage ) > 0 )? ( floor( $count / $m0r1->bonusesPerPage ) ) + 1 : $count / $m0r1->bonusesPerPage;

	$model = Product::find()->where(['main_category_id' => $m0r1->bonusCategoryID, 'active' => 1 ])->orderBy(['id' => SORT_ASC])->limit($m0r1->bonusesPerPage )->offset($page * $m0r1->bonusesPerPage);
	
	$ret = [];
	
	$bonuses = [];
	
	foreach( M0r1OrderBonuses::findAll( ['order_id'=>$order->id] ) as $bm )
	{
	    $bonuses[ $bm->product_id ] = [ 'count' => $bm->qty,  ];
	}
	
	foreach( $model->all() as $product )
	{
	    $tc = $product->hasOne(WarehouseProduct::className(),['product_id'=>'id'])->one();
	    $p = [
		'id'		=> $product->id,
		'name'		=> $product->name,
		'total_count'	=> ( is_null($tc) )? 0 : $tc->in_warehouse,
		'url'		=> Url::to(['@product','model'=>$product, 'category_group_id' => 1]),
		'img'		=> \app\m0r1\widgets\ThumbnailWidget::widget(['img' => $product->image, 'sizeid' => $m0r1->bonusesThumbSizeID, 'notfoundimg' => $m0r1->bonusesImgNotFound ]),
		'price'		=> $product->price,
		'price_p'	=> $product->currency->format($product->price),
	    ];
	    
	    $ret[] = $p;
	}
	
	$bonuses = $this->renderPartial('@app/m0r1/widgets/views/ajaxitems',[
	    'bonuses'		=> $bonuses,
	    'perpage' 		=> intval($m0r1->bonusesPerPage),
	    'perrow'  		=> intval($m0r1->bonusesPerRow),
	    'itemclasses'	=> $m0r1->bonusesItemClasses,
	    'items'   		=> $ret,
	]);
	
	return Json::encode([
	    'bonuses' => $bonuses,
	    'info' => Yii::t('m0r1','M0r1BBW Bonuses Modal Info Pattern',['page'=>( $page + 1 ),'countpage'=>$countPage]),
	    'page' => ($page + 1),
	    'countPages' => $countPage,
	]);
    }
}
