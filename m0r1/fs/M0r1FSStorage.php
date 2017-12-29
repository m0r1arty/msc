<?php

namespace app\m0r1\fs;

use Yii;

use yii\base\Component;

class M0r1FSStorage extends Component
{
    private $_storage = [];
    
    public function setValue($key,$val)
    {
	$this->_storage[$key] = $val;
    }
    
    public function getValue($key)
    {
	if( isset( $this->_storage[$key] ) )
	    return $this->_storage[$key];
	
	return NULL;
    }
}