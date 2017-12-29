<?php
namespace app\m0r1\widgets;

use Yii;

use yii\base\Widget;
use yii\db\Query;
use yii\db\Expression;

use yii\bootstrap\Modal;

use app\m0r1\helpers\M0r1Bonuses;

use app\modules\shop\models\Currency;
use app\modules\m0r1\models\M0r1OrderBonuses;

use yii\base\InvalidConfigException;

class M0r1BonusButtonWidget extends Widget
{
    public $order = NULL;
    public $viewFile = 'm0r1buttonbonuswidget';
    public $isAdmin = false;
    
    public function init()
    {
	parent::init();
    }
    
    public function run()
    {
	
	if( ! ( is_object($this->order) && get_class($this->order) == \app\modules\shop\models\Order::className() ) )
	{
	    throw new InvalidConfigException;
	}
	
	$bonuses = Currency::findOne( Yii::$app->getModule('m0r1')->bonusCurrencyID );
	
	$bprice = M0r1Bonuses::getBPrice($this->order);
	
	$view = $this->getView();
	
	$modalid = Modal::$autoIdPrefix.Modal::$counter;
	
	$adminappender = $this->isAdmin ? ','.$this->order->id : '';
	
	$view->registerAssetBundle('app\m0r1\assets\M0r1BBWAsset');
	$view->registerJs(<<<JS
	var g_M0r1BBW = new M0r1BBW('{$modalid}','m0r1bbwid'{$adminappender});
JS
,\yii\web\View::POS_END);
	
	return $this->render($this->viewFile,['bonuses' => $bonuses, 'bprice' => $bprice, 'modalid' => $modalid, 'isAdmin' => $this->isAdmin  ]);
    }
}