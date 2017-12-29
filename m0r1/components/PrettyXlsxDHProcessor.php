<?php

namespace app\m0r1\components;

use Yii;
use yii\base\Component;

class PrettyXlsxDHProcessor extends Component
{
    
    public static $translatorArray = [
	'vendor' 	=> 'Производитель',
	'name'		=> 'Наименование',
    ];
    
    
    public static function processHeader($dh = [])
    {
	$ret = [];

	foreach( $dh as $el )
	{
	    if ( isset( static::$translatorArray[ $el ] ) )
	    {
		$ret[] = static::$translatorArray[ $el ];
	    }else{
		$ret[] = $el;
	    }
	}
	
	return $ret;
    }
    
    public static function processReverseHeader($dh = [])
    {
	$ta = array_flip(static::$translatorArray);
	$ret = [];
	
	foreach( $dh as $el )
	{
	    if ( isset( $ta[ $el ] ) )
	    {
		$ret[] = $ta[ $el ];
	    }else{
		$ret[] = $el;
	    }
	}
	
	return $ret;
    }
}