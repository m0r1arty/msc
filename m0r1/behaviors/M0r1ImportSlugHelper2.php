<?php

namespace app\m0r1\behaviors;

use Yii;
use yii\base\Behavior;

use app\components\Helper;

class M0r1ImportSlugHelper2 extends Behavior
{
    private $_names = [];
    
    public function createSlug($name)
    {
	$name = Helper::createSlug($name);
	
	if( isset( $this->_names[ $name ] ) )
	{
	    $oldname = $name;
	    $name = $name.'-'.$this->_names[ $name ];
	    
	    $this->_names[ $oldname ] = intval( $this->_names[ $oldname ] ) + 1;
	}else{
	    $this->_names[ $name ] = 1;
	}
	
	return $name;
    }
}