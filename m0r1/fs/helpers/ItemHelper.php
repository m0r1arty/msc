<?php

namespace app\m0r1\fs\helpers;

use Yii;
use ReflectionClass;

class ItemHelper
{
    
    public static function getItems( $classname, $classshortname  )
    {
	$ret = [];
	
	$class = new ReflectionClass( $classname );
	
	foreach( $class->getMethods() as $m )
	{
	    $name = $m->name;
	    
	    if ( substr($name,0,4) == 'item' && strlen( $name ) > 4 )
	    {
		$ret[] = [
		    'id'	=> $classshortname.'|'.substr($name,4),
		    'name'	=> substr($name,4),
		    'comment'	=> $m->getDocComment(),
		];
	    }
	}
	
	return $ret;
    }
    
    public static function getItem( $classname, $classshortname, $itemname )
    {
	$items = static::getItems( $classname, $classshortname );
	
	foreach( $items as $item )
	{
	    if ( $item['name'] === $itemname )
	    {
		return $item;
	    }
	}
	return null;
    }
    
    public static function getView($classname,$classshortname,$item,$params = [])
    {
	$class = Yii::createObject($classname,[],[]);
	
	if( $class->hasMethod( 'view'.$item ) )
	{
	    
	    $ret = Yii::$container->invoke( [ $classname,'view'.$item ], ['params'=>$params] );
	    
	}else{
	    
	    $item = static::getItem( $classname, $classshortname, $item);
	    
	    $ret = '<pre>'.$item['comment'].'</pre>';
	    
	}
	
	return $ret;
    }
}