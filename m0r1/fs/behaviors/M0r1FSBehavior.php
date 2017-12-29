<?php

namespace app\m0r1\fs\behaviors;

use Yii;

use yii\base\Behavior;

use app\m0r1\fs\helpers\StrgHlp;

class M0r1FSBehavior extends Behavior
{
    public static $m0r1StrategyAlreadyProcessed = 0;
    
    public static function beforeUpdate(\yii\base\ModelEvent $event)
    {
	static $step = 1;

	$step++;
	
	if( $step !== 3 )
	    return;

	if( static::$m0r1StrategyAlreadyProcessed === 1 )
	    return;

	static::$m0r1StrategyAlreadyProcessed = 1;
	StrgHlp::run( $event->sender );
    }
}