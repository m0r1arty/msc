<?php

namespace app\modules\m0r1\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;

use app\m0r1\fs\models\M0r1FSConf;

use app\backend\components\BackendController;
use app\components\SearchModel;

class BackendFsController extends BackendController
{
    public function behaviors()
    {
	return [
	    'access'	=> [
		'class'		=> AccessControl::className(),
		'rules'		=> [
		    [
			'allow'	=> true,
			'roles'	=> ['finance system manage'],
		    ],
		],
	    ],
	    'verbs'	=> [
		'class'		=> VerbFilter::className(),
		'actions'	=> [
		    'delete'	=> ['post'],
		],
	    ],
	];
    }
    
    public function actionIndex()
    {
	$searchModel = new SearchModel(
	    [
		'model'				=> M0r1FSConf::className(),
		'partialMatchAttributes'	=> ['name'],
	    ]
	);
	
	$dataProvider	= $searchModel->search(Yii::$app->request->queryParams);
	
	return $this->render('index',
	[
	    'searchModel'	=> $searchModel,
	    'dataProvider'	=> $dataProvider,
	]);
    }
    
    public function actionEdit( $id = null )
    {
	if( is_null( $id ) )
	{
	    $model = new M0r1FSConf;
	}else{
	    $model = M0r1FSConf::findOne( $id );
	}
	
	if( $model->load( Yii::$app->request->post() ) )
	{
	    if( $model->save() )
	    {
		Yii::$app->session->setFlash('success',Yii::t('app','Record has been saved'));
		$returnUrl = Yii::$app->request->get( 'returnUrl',[ 'index', 'id'=>$model->id ] );
		
		switch( Yii::$app->request->post('action','save') )
		{
		    case 'next':
		    
			return $this->redirect([
			    'edit',
			    'returnUrl'	=> $returnUrl,
			]);
			
		    break;
		    case 'back':
			
			return $this->redirect($returnUrl);
			
		    break;
		    default:
			
			return $this->redirect(
			    Url::to(
				[
				    'edit',
				    'id'	=> $model->id,
				    'returnUrl'	=> $returnUrl,
				]
			    )
			);
			
		    break;
		}
		
	    }else{
		Yii::$app->session->setFlash('error',Yii::t('app','Cannot save data'));
	    }
	}
	
	return $this->render('edit',['model'=>$model]);
    }
    
    public function actionRemoveAll()
    {
	$items = Yii::$app->request->post('items',[]);
	
	if( !empty( $items ) )
	{
	    $items = M0r1FSConf::find()->where(['in','id',$items])->all();
	    
	    foreach( $items as $item )
	    {
		$item->delete();
	    }
	}
	$this->redirect(['index']);
    }
    
    public function actionDelete($id)
    {
	
	$model = M0r1FSConf::findOne($id);
	Yii::trace($model);
	
	if( !is_null( $model ) )
	{
	    $model->delete();
	}
	
	return $this->redirect(['index']);
    }
}