<?php

namespace app\m0r1\behaviors;

use Yii;
use yii\base\Behavior;

class M0r1ImportModelBehavior extends Behavior
{
    public $m0r1Sorting = [];
    
    public function events()
    {
	return [
	    \app\modules\data\models\ImportModel::EVENT_BEFORE_SERIALIZE 	=> 'm0r1BeforeSerialize',
	    \app\modules\data\models\ImportModel::EVENT_BEFORE_LOAD 		=> 'm0r1BeforeLoad',
	    \app\modules\data\models\ImportModel::EVENT_AFTER_UNSERIALIZE	=> 'm0r1AfterUnserialize',
	];
    }
    
    public function m0r1BeforeSerialize($event)
    {
	$this->owner->_serializeArray['m0r1Sorting'] = $this->m0r1Sorting;
    }
    
    public function m0r1BeforeLoad(\app\modules\data\components\ImportModelLoadEvent $event)
    {
	$data = $event->_data;
	$formName = $event->_formName;
	
        if( isset( $data['ImportModel'] ) && isset( $data['ImportModel']['m0r1Sorting'] ) && count( $data['ImportModel']['m0r1Sorting'] ) > 0 )
        {
	    foreach( $data['ImportModel']['m0r1Sorting'] as $mskey )
            {
        	$tmp = explode('_',$mskey);

                $tmpKey = $tmp[0].'|';
                unset($tmp[0],$tmp[1],$tmp[2]);

                $tmpKey .= implode('_',$tmp);

                $this->m0r1Sorting[] = $tmpKey;
            }
        }
    }

    public function m0r1AfterUnserialize(\app\modules\data\components\ImportModelAUnserializeEvent $event)
    {
	$fields = $event->fields;
	
	$this->m0r1Sorting = isset( $fields['m0r1Sorting'] )? $fields['m0r1Sorting'] : [] ;
	
	$this->owner->fields['m0r1Sorting'] = $this->m0r1Sorting;
    }
}