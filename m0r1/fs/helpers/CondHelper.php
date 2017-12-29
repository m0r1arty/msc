<?php

namespace app\m0r1\fs\helpers;

use Yii;
use ReflectionClass;

use app\m0r1\fs\models\M0r1FSConds;
use app\m0r1\fs\models\M0r1FSTube;

class CondHelper
{
    public static function getItems( $sname , $classname )
    {
	$ret = [];
	
	$class = new ReflectionClass( $classname );
	
	foreach( $class->getMethods() as $m )
	{
	    $name = $m->name;
	    
	    if( 
		( strlen( $name ) > 4 && substr( $name, 0, 4 ) === 'cond' && $len = 4 )
		||
		( strlen( $name ) > 7 && substr( $name, 0, 7 ) === 'valCond' && $len = 7  )
		||
		( strlen( $name ) > 8 && substr( $name, 0, 8 ) === 'exprCond' && $len = 8 )
	    )
	    {
		$shortname = substr( $name, $len);
		$ret[] = [
		    'name'		=> $shortname,
		    'description'	=> Yii::t('m0r1','M0r1 FS System Tube Cond '.$sname.'|'.$shortname),
		    'type'		=> substr( $name, 0, $len ),
		];
	    }
	}
	
	return $ret;
    }
    
    public static function getView( $cond, $pos )
    {
	$ret = '';
	
	$model = M0r1FSConds::findOne($cond['cond_id']);
	
	if( !is_null( $model ) )
	{
	    $cond['cond_name'] = $model->name;
	    $cond['pos'] = $pos;
	    
	    if( $cond['act'] === 'tube' )
	    {
		$model = M0r1FSTube::findOne( intval( $cond['tube_id'] ) );
		
		if( !is_null( $model ) )
		{
		    $cond['tube'] = $model->name;
		}
	    }
	    
	    $ret = Yii::$app->controller->renderPartial('_part_cond',$cond);
	}
	
	
	return $ret;
    }
}