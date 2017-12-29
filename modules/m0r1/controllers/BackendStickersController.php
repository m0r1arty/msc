<?php

namespace app\modules\m0r1\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\helpers\Url;

use app\modules\image\widgets\SaveInfoAction;
use app\backend\components\BackendController;


class BackendStickersController extends BackendController
{
    public function behaviors()
    {
	return [
	    'access'	=> [
		'class'		=> AccessControl::className(),
		'rules'		=> [
		    [
			'allow'	=> true,
			'roles'	=> ['order manage'],
		    ],
		],
	    ],
	];
    }
    
    public function actions()
    {
	return [
	    'save-info'		=> [
		'class' => SaveInfoAction::className(),
	    ]
	];
    }
    
    public function actionIndex()
    {
	
	if( Yii::$app->request->isPost )
	{
	    $this->runAction( 'save-info', [ 'model_id' => 0, ] );
	}
	
	return $this->render('index');
    }
}