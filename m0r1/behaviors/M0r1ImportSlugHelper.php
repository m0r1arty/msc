<?php

namespace app\m0r1\behaviors;

use Yii;
use yii\base\Behavior;

use app\components\Helper;

class M0r1ImportSlugHelper extends Behavior
{
    public function createSlug($name)
    {
	$rnd = dechex(rand());
	
	$name = Helper::createSlug($name);
	
	if( strlen($name) + strlen($rnd) > 80 )
	{
	    $name = substr($name,0, 80 - strlen($rnd));
	    $name .= $rnd;
	}else{
	    $name .= $rnd;
	}
	
	return $name;
    }
}