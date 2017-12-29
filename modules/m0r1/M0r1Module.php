<?php

namespace app\modules\m0r1;

use Yii;

use yii\base\Event;
use yii\base\BootstrapInterface;

use app;
use app\m0r1\behaviors\M0r1CartBonusesBehavior;
use app\m0r1\fs\behaviors\M0r1FSBehavior;
use app\m0r1\behaviors\M0r1AccDiscountBehavior;
use app\components\BaseModule;
use app\modules\shop\controllers\CartController;
use app\modules\shop\models\Order;

/**
 *	M0r1M0dule
 * 	@package app\modules\m0r1
 */

class M0r1Module extends BaseModule implements BootstrapInterface
{
    
    const ACC_STRATEGY_PRODUCT = 'product';
    const ACC_STRATEGY_ORDER   = 'order';
    
    public $exportKeyPropertyID = 161;
    public $exportCurrencyID = 3;
    public $imgPropertySizeID = 1;
    public $bonusCurrencyID = 1;
    public $bonusCategoryID = 1;
    public $bonusesPerRow = 3;
    public $bonusesPerPage = 9;
    public $bonusesItemClasses = 'col-md-4';
    public $bonusesThumbSizeID = 1;
    public $bonusesImgNotFound = 'https://placeholdit.imgix.net/~text?txtsize=10&txt=Image+not+found&w=80&h=80';
    public $fsConfID = 1;
    public $accStrategy = 'order';
    public $accDiscountStageId = 0;
    public $stickerThumbSizeID = 0;
    public $magicTubeID	= 0;
    public $magicPropertyID = 0;
    public $dummyStrategyID = 0;
    
    public $controllerMap = [
	'ajax' => 'app\modules\m0r1\controllers\AjaxController',
    ];
    
    public function bootstrap($app)
    {
	Event::on(CartController::className(),CartController::EVENT_ACTION_ADD,[M0r1CartBonusesBehavior::className(),'m0r1CalculateCurrentBonuses']);
	Event::on(CartController::className(),CartController::EVENT_ACTION_REMOVE,[M0r1CartBonusesBehavior::className(),'m0r1CalculateCurrentBonuses']);
	Event::on(CartController::className(),CartController::EVENT_ACTION_QUANTITY,[M0r1CartBonusesBehavior::className(),'m0r1CalculateCurrentBonuses']);
	Event::on(CartController::className(),CartController::EVENT_ACTION_CLEAR,[M0r1CartBonusesBehavior::className(),'m0r1CalculateCurrentBonuses']);
	Event::on(Order::className(),Order::EVENT_BEFORE_UPDATE,[M0r1AccDiscountBehavior::className(),'beforeUpdate']);
	Event::on(Order::className(),Order::EVENT_BEFORE_UPDATE,[M0r1FSBehavior::className(),'beforeUpdate']);
    }
    
    public function behaviors()
    {
	return [
	    'configurableModule' 	=> [
		'class'			=> 'app\modules\config\behaviors\ConfigurableModuleBehavior',
		'configurationView'	=> '@app/modules/m0r1/views/configurable/_config',
		'configurableModel'	=> 'app\modules\m0r1\models\M0r1ConfigurableModel',
	    ],
	];
    }
    /**
     *
     */
    public function init()
    {
	parent::init();
	
	if( Yii::$app instanceof \yii\console\Application )
	{
	    $this->controllerMap = [];
	}
    }
    
    
}
