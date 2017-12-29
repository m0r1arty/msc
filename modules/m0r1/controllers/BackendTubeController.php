<?php

namespace app\modules\m0r1\controllers;

use Yii;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Json;

use yii\db\Expression;
use yii\db\Query;

use app\m0r1\fs\models\M0r1FSTube;
use app\m0r1\fs\models\M0r1FSItems;
use app\m0r1\fs\models\M0r1FSTubeItem;
use app\m0r1\fs\models\M0r1FSConds;

use app\m0r1\fs\helpers\ItemHelper;
use app\m0r1\fs\helpers\CondHelper;

use app\backend\components\BackendController;
use app\components\SearchModel;

class BackendTubeController extends BackendController
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
		'model'				=> M0r1FSTube::className(),
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
	
	\app\m0r1\assets\M0r1ItemAsset::register(Yii::$app->getView());
	\app\m0r1\assets\M0r1ItemDiscountAsset::register(Yii::$app->getView());
	
	if( is_null( $id ) )
	{
	    $model = new M0r1FSTube;
	}else{
	    
	    $id = intval( $id );
	    $model = M0r1FSTube::findOne( $id );
	    
	}
	
	
	if( $model->load( Yii::$app->request->post() ) )
	{
	    if( $model->save() )
	    {
		Yii::$app->session->setFlash( 'success', Yii::t('app','Record has been saved') );
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
	
	$tubeitems = [];
	
	$timodel = M0r1FSTubeItem::find()->where( ['tube_id' => $id ] )->orderBy('pos',SORT_ASC);
	
	foreach( $timodel->all() as $ti )
	{
	    $obj = Json::decode($ti->params);
	    $conditions = [];
	    
	    if( !is_null( $obj ) )
	    {
		if( isset( $obj['conditions'] ) && is_array( $obj['conditions'] ) )
		{
		    foreach( $obj['conditions'] as $pos => $cond )
		    {
			$conditions[] = CondHelper::getView( $cond, $pos );
		    }
		}
	    }
	    
	    $params = [];
	    
	    $obj = Json::decode( $ti->params );
	    
	    if( !is_null( $obj  ) )
	    {
		$params = $obj;
		
		if( isset( $params['conditions'] ) )
		    unset($params['conditions']);
	    }
	    
	    $tubeitems[] = $this->renderPartial('_part_tubeitem',[ 
		'id' => $ti->id, 'pos' => $ti->pos, 
		'name' => '['.$ti->item->name.'|'.$ti->name.']', 
		'view'=> ItemHelper::getView($ti->item->class,$ti->item->name,$ti->name,$params), 
		'conditions' => $conditions,
	    ]);
	}
	
	Yii::$app->getView()->registerJS(<<<JS
	var g_M0r1Item = new M0r1Item({$id});
JS
,\yii\web\View::POS_END);
	
	return $this->render('edit',['model'=>$model,'items' => M0r1FSItems::find()->all(),'tubeitems' => $tubeitems ]);
    }
    
    public function actionRemoveAll()
    {
	$items = Yii::$app->request->post('items',[]);
	
	if( !empty( $items ) )
	{
	    $items = M0r1FSTube::find()->where(['in','id',$items])->all();
	    
	    foreach( $items as $item )
	    {
		$item->delete();
	    }
	}
	$this->redirect(['index']);
    }
    
    public function actionDelete($id)
    {
	
	$model = M0r1FSTube::findOne($id);
	Yii::trace($model);
	
	if( !is_null( $model ) )
	{
	    $model->delete();
	}
	
	return $this->redirect(['index']);
    }
    
    public function actionAjax()
    {
	$ret = [];
	
	$post = Yii::$app->request->post();
	
	if( isset( $post['action'] ) )
	{
	    switch( $post['action'] )
	    {
		
		case 'addCondition':
		    
		    $tiid = intval( $post['id'] );
		    $condid = intval( $post['cond_id'] );
		    $cond_item = $post['cond_item'];
		    
		    $condItem = [
			'tiid'		=> $tiid,
			'cond_id'	=> $condid,
			'item'		=> $cond_item,
			'type'		=> $post['type'],
		    ];
		    
		    switch( $post['type'] )
		    {
			case 'valCond':
			    
			    $condItem['val'] = $post['val'];
			    
			break;
			
			case 'exprCond':
			    
			    $condItem['expr'] = $post['expr'];
			    $condItem['val'] = $post['val'];
			    
			break;
		    }
		    
		    $condItem['act']	= $post['act'];
		    
		    if( $post['act'] === 'tube' )
		    {
			$condItem['tube_id'] = intval($post['tubeid']);
		    }
		    
		    $model = M0r1FSTubeItem::findOne($tiid);
		    
		    if( !is_null( $model ) )
		    {
			$obj = Json::decode( $model->params );
			
			if( is_null( $obj ) )
			{
			    $obj = [
				'conditions'	=> [$condItem],
			    ];
			}else{
			    $obj['conditions'][] = $condItem;
			}
			
			$model->params = Json::encode( $obj );
			
			if( $model->save() )
			{
			    $ret['success'] = 1;
			    $ret['id'] = $tiid;
			    $ret['view'] = CondHelper::getView( $condItem, count( $obj['conditions'] ) - 1  );
			}
		    }
		    
		break;

		case 'changeCondition':
		    
		    $tiid = intval( $post['id'] );
		    $pos = intval( $post['pos'] );
		    $condid = intval( $post['cond_id'] );
		    $cond_item = $post['cond_item'];
		    
		    $condItem = [
			'tiid'		=> $tiid,
			'cond_id'	=> $condid,
			'item'		=> $cond_item,
			'type'		=> $post['type'],
		    ];
		    
		    switch( $post['type'] )
		    {
			case 'valCond':
			    
			    $condItem['val'] = $post['val'];
			    
			break;
			
			case 'exprCond':
			    
			    $condItem['expr'] = $post['expr'];
			    $condItem['val'] = $post['val'];
			    
			break;
		    }
		    
		    $condItem['act']	= $post['act'];
		    
		    if( $post['act'] === 'tube' )
		    {
			$condItem['tube_id'] = intval($post['tubeid']);
		    }
		    
		    $model = M0r1FSTubeItem::findOne($tiid);
		    
		    if( !is_null( $model ) )
		    {
			$obj = Json::decode( $model->params );
			
			if( !is_null( $obj ) && isset( $obj['conditions'] ) && is_array( $obj['conditions'] ) && isset( $obj['conditions'][$pos] ) )
			{
			    $obj['conditions'][$pos] = $condItem;
			    
			    $model->params = Json::encode( $obj );
			    
			    if( $model->save() )
			    {
				$ret['success'] = 1;
				$ret['id'] = $tiid;
				$ret['pos'] = $pos;
				$ret['view'] = CondHelper::getView( $condItem, $pos  );
			    }
			}
		    }
		    
		break;
		
		case 'reorderCondition':
		    
		    $tiid = intval( $post['id'] );
		    $oldpos = intval( $post['pos'] );
		    $newpos = intval( $post['newpos'] );
		    $dir = $post['direction'];
		    
		    $model = M0r1FSTubeItem::findOne( $tiid );
		    
		    if( !is_null( $model ) )
		    {
			$obj = Json::decode( $model->params );
			
			if( !is_null( $obj ) && isset( $obj['conditions'] ) && is_array( $obj['conditions'] ) && isset( $obj['conditions'][$oldpos] ) && isset( $obj['conditions'][$newpos] ) )
			{
			    $conds = [];
			    
			    $item = $obj['conditions'][$oldpos];
			    unset($obj['conditions'][$oldpos]);
			    
			    foreach( $obj['conditions'] as $k=>$v )
			    {
				if( $k !== $newpos )
				{
				    $conds[] = $v;
				}else{
				    if( $dir == 'up' )
				    {
					$conds[] = $item;
					$conds[] = $v;
				    }else{
					$conds[] = $v;
					$conds[] = $item;
				    }
				}
			    }
			    
			    $obj['conditions'] = $conds;
			    
			    $model->params = Json::encode( $obj );
			    
			    if( $model->save() )
			    {
				$ret['success'] = 1;
				$ret['dir'] = $dir;
				$ret['pos'] = $oldpos;
				$ret['id'] = $tiid;
				$ret['view'] = CondHelper::getView($item,$newpos);
			    }
			}
		    }
		    
		break;


		case 'delCondition':
		    
		    $tiid = intval( $post['id'] );
		    $pos  = intval( $post['pos'] );
		    
		    $model = M0r1FSTubeItem::findOne( $tiid );
		    
		    if( !is_null( $model ) )
		    {
			
			$obj = Json::decode( $model->params );
			
			$ret['id'] = $tiid;
			$ret['pos'] = $pos;
			
			if( !is_null( $obj ) )
			{
			    if( isset( $obj['conditions'] ) && is_array( $obj['conditions'] ) && isset( $obj['conditions'][$pos] ) )
			    {
				unset($obj['conditions'][$pos]);
				$obj['conditions'] = array_values( $obj['conditions'] );
				
				$model->params = Json::encode( $obj );
				
				$model->save();
			    }
			}
			
			$ret['success'] = 1;
		    }
		    
		break;
		
		case 'getConditions':
		    
		    $tmp = [];
		    $tmp['cnds'] = [];
		    
		    foreach( M0r1FSConds::find()->orderBy( 'id', SORT_ASC )->all() as $cnd )
		    {
			$tmp['cnds'][] = ['id'=>$cnd->id,'name'=>$cnd->name,'class'=>$cnd->class];
		    }
		    
		    $tmp['cnd0items'] = CondHelper::getItems( $tmp['cnds'][0]['name'], $tmp['cnds'][0]['class'] );
		    
		    $ret['view'] = $this->renderPartial( '_part_conds', [ 'conditions'=>$tmp['cnds'], 'cnd0items' => $tmp['cnd0items'], 'tubes' => M0r1FSTube::find()->orderBy( [ 'id' => SORT_ASC] )->all() ] );
		    $ret['placeholder'] = Yii::t('m0r1','M0r1 FS System Select Condition');
		    $ret['button'] = Yii::t('app','Add');
		    $ret['success']	= 1;
		    
		break;

		case 'getCondition':
		    
		    $pos = intval( $post['pos'] );
		    $id = intval( $post['id'] );
		    
		    $tmp = [];
		    $tmp['cnds'] = [];

		    $model = M0r1FSTubeItem::findOne( $id );
		    $item = [];
		    
		    if( !is_null( $model  ) )
		    {
			$obj = Json::decode( $model->params  );
			
			if( !is_null( $obj ) && isset( $obj['conditions'] ) && is_array( $obj['conditions'] ) && isset( $obj['conditions'][$pos] ) )
			{
			    $item = $obj['conditions'][$pos];
			    
			    $cond = M0r1FSConds::findOne( $obj['conditions'][$pos]['cond_id']  );
			    
			    if( !is_null( $cond ) )
			    {
				$tmp['cnd0items'] = CondHelper::getItems( $cond->name, $cond->class );
			    }
			}
			

		    }
		    
		    foreach( M0r1FSConds::find()->orderBy( 'id', SORT_ASC )->all() as $cnd )
		    {
			$tmp['cnds'][] = ['id'=>$cnd->id,'name'=>$cnd->name,'class'=>$cnd->class, 'selected' => ( $cnd->id == $item['cond_id'] )  ];
		    }
		    
		    $ret['view'] = $this->renderPartial( '_part_conds', [ 'edit' => true, 'conditions'=>$tmp['cnds'], 'cnd0items' => $tmp['cnd0items'], 'tubes' => M0r1FSTube::find()->orderBy( [ 'id' => SORT_ASC] )->all(), 'item' => $item ] );
		    $ret['placeholder'] = Yii::t('m0r1','M0r1 FS System Select Condition');
		    $ret['button'] = Yii::t('app','Edit');
		    $ret['success']	= 1;
		    
		break;
		
		case 'getCondItems':
		    
		    $cond_id = intval( $post['cond_id'] );
		    
		    $model = M0r1FSConds::findOne( $cond_id );
		    
		    if( !is_null( $model ) )
		    {
			$ret['view'] = $this->renderPartial('_part_cond_items',['cnd0items' => CondHelper::getItems( $model->name, $model->class )]);
			$ret['success'] = 1;
		    }
		    
		break;
		
		case 'appendItem':
		    
		    $tubeid = intval( $post['tubeid'] );
		    
		    $ids = explode( '|', $post['id'] );
		    
		    if( is_array($ids) && count($ids) == 2 )
		    {
			
			$model = M0r1FSItems::find()->where( [ 'name' => $ids[0] ] )->one();
			
			if( !is_null( $model ) )
			{
			    $class = Yii::createObject($model->class,[],[]);
			    
			    if( $class->hasMethod( 'item'.$ids[1] ) )
			    {
				$citems = M0r1FSTubeItem::find()->where(['tube_id'=>$tubeid])->count();
				
				$ti = new M0r1FSTubeItem;
				
				$ti->setAttributes([
				    'tube_id'		=> $tubeid,
				    'item_id'		=> $model->id,
				    'pos'		=> $citems,
				    'name'		=> $ids[1],
				    'params'		=> '',
				]);
				
				if( $ti->save() )
				{
				    $ret['success'] 	= 1;
				    $ret['pos']		= intval( $citems );
				    $ret['id']		= $ti->id;
				    $ret['name']	= '['.$post['id'].']';
				    $ret['ndname']	= $post['id'];
				    $ret['button']	= Yii::t('m0r1','M0r1 FS System Add Condition');
				    $ret['view']	= ItemHelper::getView($model->class,$model->name,$ids[1]);
				}/*else{
				    $ret = $ti->errors;
				}*/
			    }
			}
			
		    }
		    
		break;

		case 'insertItem':
		    
		    $tubeid = intval( $post['tubeid'] );
		    $pos = intval( $post['pos'] );
		    
		    $ids = explode( '|', $post['id'] );
		    
		    if( is_array($ids) && count($ids) == 2 )
		    {
			
			$model = M0r1FSItems::find()->where( [ 'name' => $ids[0] ] )->one();
			
			if( !is_null( $model ) )
			{
			    $class = Yii::createObject($model->class,[],[]);
			    
			    if( $class->hasMethod( 'item'.$ids[1] ) )
			    {
				Yii::$app->db->createCommand()->update( M0r1FSTubeItem::tableName(),['pos'=>new Expression('pos + 1')],'tube_id = :tubeid AND pos >= :pos',[':tubeid' => $tubeid, ':pos' => $pos ] )->execute();
				
				$ti = new M0r1FSTubeItem;
				
				$ti->setAttributes([
				    'tube_id'		=> $tubeid,
				    'item_id'		=> $model->id,
				    'pos'		=> $pos,
				    'name'		=> $ids[1],
				    'params'		=> '',
				]);
				
				if( $ti->save() )
				{
				    $ret['success'] 	= 1;
				    $ret['pos']		= $pos;
				    $ret['id']		= $ti->id;
				    $ret['name']	= '['.$post['id'].']';
				    $ret['ndname']	= $post['id'];
				    $ret['button']	= Yii::t('m0r1','M0r1 FS System Add Condition');
				    $ret['view']	= ItemHelper::getView($model->class,$model->name,$ids[1]);
				}/*else{
				    $ret = $ti->errors;
				}*/
			    }
			}
			
		    }
		    
		break;
		
		case 'confItem':
		    
		    $id = intval( $post['id'] );
		    
		    $model = M0r1FSTubeItem::findOne( $id );
		    
		    if( !is_null( $model ) )
		    {
			$ret['ndname'] = $model->item->name.'|'.$model->name;
			
			unset($post['action']);
			unset($post['id']);
			unset($post['pos']);
			
			$obj = Json::decode( $model->params  );
			
			if( is_null( $obj) )
			{
			    $obj = [ 'conditions' => [] ];
			}
			
			foreach( $post as $k => $v)
			{
			    if( $k !== 'conditions' )
			    {
				$obj[$k] = $v;
			    }
			}
			
			$model->params = Json::encode( $obj );
			
			if( $model->save() )
			{
			    $ret['success'] = 1;
			}
		    }
		    
		break;
		
		case 'reorderItem':
		    
		    $id = intval( $post['id'] );
		    $tubeid = intval( $post['tubeid'] );
		    $pos = intval( $post['pos'] );
		    $newpos = intval( $post['newpos'] );
		    $dir = $post['direction'];
		    
		    $model = M0r1FSTubeItem::findOne( $id );
		    
		    if( !is_null($model) && in_array( $dir, ['down','up'] ) )
		    {
			$ret['id'] = $id;
			$ret['pos'] = $newpos;
			$ret['dir'] = $dir;
			
			if( $dir === "up" )
			{
			    
			    Yii::$app->db->createCommand()->update( M0r1FSTubeItem::tableName(),['pos'=>new Expression('pos + 1')],'tube_id = :tubeid AND pos < :pos AND pos >= :newpos',[':tubeid' => $tubeid, ':pos' => $pos, ':newpos' => $newpos ] )->execute();
			    
			}else if( $dir === "down")
			{
			    Yii::$app->db->createCommand()->update( M0r1FSTubeItem::tableName(),['pos'=>new Expression('pos - 1')],'tube_id = :tubeid AND pos > :pos AND pos <= :newpos',[':tubeid' => $tubeid, ':pos' => $pos,':newpos' => $newpos ] )->execute();
			}
			
			$model->pos = $newpos;
			
			if( $model->save() )
			{
			    $ret['success'] = 1;
			}
		    }
		    
		break;

		
		case 'delItem':
		    
		    $tubeid = intval( $post['tubeid'] );
		    $pos = intval( $post['pos'] );
		    $id = intval( $post['id'] );
		    
		    $model = M0r1FSTubeItem::findOne($id);
		    
		    if( !is_null( $model ) )
		    {
			if ( $model->delete() !== FALSE )
			{
			    Yii::$app->db->createCommand()->update( M0r1FSTubeItem::tableName(),['pos'=>new Expression('pos - 1')],'tube_id = :tubeid AND pos > :pos',[':tubeid' => $tubeid, ':pos' => $pos ] )->execute();
			    $ret['success'] = 1;
			    $ret['id'] = $id;
			}
		    }
		    
		break;
	    }
	}
	
	
	return Json::encode($ret);
    }
    
}