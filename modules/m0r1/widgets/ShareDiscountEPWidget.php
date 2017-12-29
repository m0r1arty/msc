<?php

namespace app\modules\m0r1\widgets;

use Yii;

use yii\base\Widget;

class ShareDiscountEPWidget extends Widget
{
    public static $_m0r1Inited = NULL;
    public $model;
    
    public function init()
    {
	parent::init();
    }
    
    public function run()
    {
	$out = '';
	
	if( is_null(static::$_m0r1Inited) )
	{
	    static::$_m0r1Inited = true;
	    
	    $view = Yii::$app->getView();
	    
	    $view->registerAssetBundle('yii\bootstrap\BootstrapAsset');
	    
	    $out .= $view->render('@app/modules/m0r1/views/widgets/_sharediscountepwidgetmodal.php',['model'=>$this->model]);
	}
	
	$out .= $view->render('@app/modules/m0r1/views/widgets/_sharediscountepwidgetbutton.php',['model'=>$this->model]);
	
	return $out;
    }
}