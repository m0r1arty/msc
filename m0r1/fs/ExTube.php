<?php

namespace app\m0r1\fs;

use Yii;

use yii\base\Component;
use yii\helpers\Json;

use yii\base\InvalidConfigException;


use app\m0r1\fs\helpers\StrgHlp;

use app\m0r1\fs\models\M0r1FSConds;
use app\m0r1\fs\models\M0r1FSTubeItem;

use app\m0r1\fs\M0r1FSProductBundle;
use app\m0r1\fs\M0r1FSThingBundle;

class ExTube extends Component
{
    private $_steps = [];
    private $_tube_id = 0;
    
    private static $_ex_items = [];
    private static $_ex_conds = [];
    
    public function setTube_id($tube_id)
    {
	$this->_tube_id = intval( $tube_id );
    }
    
    public function init()
    {
	if( $this->_tube_id === 0 )
	{
	    throw new InvalidConfigException;
	}
	
	foreach( M0r1FSTubeItem::find()->where( [ 'tube_id' => $this->_tube_id ] )->orderBy( [ 'pos' => SORT_ASC ] )->all() as $ti )
	{
	    if ( !isset( static::$_ex_items[ $ti->item_id ] ) )
	    {
		static::$_ex_items[ $ti->item_id ] = $ti->item;
	    }
	    
	    $params = Json::decode( $ti->params );
	    
	    if( is_null( $params ) )
	    {
		$params = [];
	    }
	    
	    if( isset( $params['conditions'] ) )
	    {
		$conditions = $params['conditions'];
		
		unset( $params['conditions'] );
	    }else{
		$conditions = null;
	    }
	    
	    $step = [
		'item_id'	=> $ti->item_id,
		'item_name'	=> $ti->name,
		'params'	=> $params,
	    ];
	    
	    if( !is_null( $conditions ) )
	    {
		$step['conditions'] = $conditions;
	    }
	    
	    $this->_steps[] = $step;
	}
	
	reset($this->_steps);
    }
    
    public function step( M0r1FSProductBundle $pbundle, M0r1FSThingBundle $tbundle )
    {
	$step = current($this->_steps);
	
	$tube = $this;
	
	if( $step !== FALSE )
	{
	    
	    $ret = Yii::$container->invoke( [ static::$_ex_items[ $step[ 'item_id' ] ]->class, 'item'.$step['item_name'] ],[
		'pbundle'	=> $pbundle,
		'tbundle'	=> $tbundle,
		'params'	=> $step['params'],
	    ] );
	    
	    if( isset( $step['conditions'] ) )
	    {
		foreach( $step['conditions'] as $cond )
		{
		    
		    $cond_id = intval( $cond['cond_id'] );
		    
		    if( !isset( static::$_ex_conds[ $cond_id ] ) )
		    {
			static::$_ex_conds[ $cond_id ] = M0r1FSConds::findOne( $cond_id );
		    }
		    
		    switch( $cond['type'] )
		    {
			case 'cond':
			    
			    $ret2 = Yii::$container->invoke( [ static::$_ex_conds[ $cond_id ]->class, 'cond'.$cond['item'] ],[]);
			    
			break;
			
			case 'valCond':
			    
			    $ret2 = Yii::$container->invoke( [ static::$_ex_conds[ $cond_id ]->class, 'valCond'.$cond['item'] ],[
				'val'		=> $cond['val'],
			    ]);
			    
			break;
			
			case 'exprCond':
			    
			    $ret2 = Yii::$container->invoke( [ static::$_ex_conds[ $cond_id ]->class, 'exprCond'.$cond['item'] ],[
				'expr'		=> $cond['expr'],
				'val'		=> $cond['val'],
			    ]);
			    
			break;
		    }
		    
		    if( $ret2 )
		    {
			if( $cond['act'] === 'cont' )//continue
			{
			    break;//foreach( $step['conditions'] as $cond )
			}else if( $cond['act'] === 'ret' )
			{
			    $tube = null;
			    break;
			}else if( $cond['act'] === 'tube' )
			{
			    $tube = StrgHlp::getExTube( intval( $cond['tube_id'] ) );
			    break;
			}
		    }
		    
		}
	    }
	    
	    next( $this->_steps );
	}else {
	    return null;
	}
	
	return $tube;
    }
}

