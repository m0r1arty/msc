<?php

namespace app\modules\m0r1\models;

use Yii;
use yii\helpers\ArrayHelper;

use app\m0r1\fs\models\M0r1FSConf;
use app\m0r1\fs\models\M0r1FSTube;

use app\modules\config\models\BaseConfigurationModel;

use app\modules\shop\models\Currency;
use app\modules\image\models\ThumbnailSize;
use app\modules\shop\models\Category;
use app\models\Property;

class M0r1ConfigurableModel extends BaseConfigurationModel
{
    public $exportKeyPropertyID;
    public $exportCurrencyID;
    public $imgPropertySizeID;
    public $bonusCurrencyID;
    public $bonusCategoryID;
    public $bonusesPerRow;
    public $bonusesPerPage;
    public $bonusesItemClasses;
    public $bonusesThumbSizeID;
    public $bonusesImgNotFound;
    public $fsConfID;
    public $accStrategy;
    public $accDiscountStageId;
    public $stickerThumbSizeID;
    public $magicTubeID;
    public $magicPropertyID;
    public $dummyStrategyID;
    
    public function rules()
    {
	return [
	    [['exportKeyPropertyID','exportCurrencyID','imgPropertySizeID','bonusCurrencyID','bonusCategoryID','bonusesPerRow','bonusesPerPage','bonusesThumbSizeID','accDiscountStageId','stickerThumbSizeID'],'integer'],
	    [['magicTubeID','magicPropertyID','dummyStrategyID'],'integer'],
	    [['bonusesItemClasses','bonusesImgNotFound','accStrategy'],'string']
	];
    }
    
    public function attributeLabels()
    {
	return [
	    'exportKeyPropertyID' 	=> Yii::t('m0r1','M0r1 Settings Configurable Export Property ID'),
	    'exportCurrencyID' 		=> Yii::t('m0r1','M0r1 Settings Configurable Export Currency'),
	    'imgPropertySizeID' 	=> Yii::t('m0r1','M0r1 Settings Configurable Property Image Size ID'),
	    'bonusCurrencyID'		=> Yii::t('m0r1','M0r1 Settings Configurable Bonus Currency ID'),
	    'bonusCategoryID'		=> Yii::t('m0r1','M0r1 Settings Configurable Bonus Category ID'),
	    'bonusesPerRow'		=> Yii::t('m0r1','M0r1 Settings Configurable Bonuses Per Row'),
	    'bonusesPerPage'		=> Yii::t('m0r1','M0r1 Settings Configurable Bonuses Per Page'),
	    'bonusesItemClasses'	=> Yii::t('m0r1','M0r1 Settings Configurable Bonuses Item Classes'),
	    'bonusesThumbSizeID'	=> Yii::t('m0r1','M0r1 Settings Configurable Bonuses ThumbnailSize'),
	    'fsConfID'			=> Yii::t('m0r1','M0r1 Settings Configurable FS Configuration'),
	    'accStrategy'		=> Yii::t('m0r1','M0r1 Accumulative Discount Strategy'),
	    'accDiscountStageId'	=> Yii::t('m0r1','M0r1 Accumulative Discount Stage Id'),
	    'stickerThumbSizeID'	=> Yii::t('m0r1','M0r1 Order Stickers Thumbnail Size ID'),
	    'magicTubeID'		=> Yii::t('m0r1','M0r1 Magic Property Tube'),
	    'magicPropertyID'		=> Yii::t('m0r1','M0r1 Magic Property Count ID'),
	    'dummyStrategyID'		=> Yii::t('m0r1','M0r1 Dummy Strategy ID'),
	];
    }
    
    public function getAccStages()
    {
	return ArrayHelper::map( \app\modules\shop\models\OrderStage::find()->orderBy( ['id' => SORT_ASC] )->all(),'id','name_frontend');
    }
    
    public function getAccStrategies()
    {
	$ret = [];
	
	foreach( ['product','order'] as $str )
	{
	    $ret[] = [ 'id' => $str, 'value' => Yii::t('m0r1','M0r1 Accumulative Discount Strategy '.ucfirst($str)) ];
	}
	return ArrayHelper::map( $ret, 'id', 'value' );
    }
    
    public function getCurrenciesArray()
    {
	$model = Currency::find()->orderBy(['sort_order'=>SORT_ASC])->asArray();
	
	return ArrayHelper::map( $model->all(), 'id', 'name' );
    }
    
    public function getCategoriesArray()
    {
	$ret = [];
	
	$model = Category::find()->orderBy(['id'=>SORT_ASC]);
	
	foreach( $model->all() as $cat )
	{
	    $ret[] = ['id' => $cat->id , 'value' => '('.$cat->id.') '.$cat->name];
	}
	
	return ArrayHelper::map( $ret, 'id' , 'value' );
    }
    
    
    public function getPropertiesArray()
    {
	$model = Property::find();
	
	$ret = [];
	
	foreach( $model->all() as $prop )
	{
	    $ret[] = [ 'id' => $prop->id, 'value' => $prop->name.'('.$prop->id.')'.'['.$prop->group->name.']'.'['.$prop->group->object->name.']'];
	}
	
	return ArrayHelper::map( $ret, 'id' , 'value' );
    }
    
    public function getSizesIDs()
    {
	$model = ThumbnailSize::find()->orderBy(['id'=>SORT_ASC]);
	
	$ret = [];
	
	foreach( $model->all() as $ts )
	{
	    $ret[] = [ 'id'=> $ts->id, 'value' => $ts->width.' x '.$ts->height ];
	}
	
	return ArrayHelper::map( $ret, 'id' , 'value' );
    }
    
    public function getAllFSConfs()
    {
	$model = M0r1FSConf::find()->orderBy( [ 'name' => SORT_ASC ] );
	
	$ret = [];
	
	foreach( $model->all() as $fsc )
	{
	    $ret[] = [ 'id' => $fsc->id, 'value' => $fsc->name ];
	}
	
	return ArrayHelper::map( $ret, 'id', 'value' );
    }
    
    public function getTubeIDS()
    {
	$model = M0r1FSTube::find()->orderBy( [ 'name' => SORT_ASC ] );
	
	$ret = [];
	
	foreach( $model->all() as $tube )
	{
	    $ret[] = [ 'id' => $tube->id, 'value' => $tube->name ];
	}
	
	return ArrayHelper::map( $ret, 'id', 'value' );
    }
    
    public function defaultValues()
    {
	$module = Yii::$app->getModule('m0r1');
	
	$attributes = array_keys($this->getAttributes());
	
	foreach( $attributes as $attribute )
	{
	    $this->{$attribute} = $module->{$attribute};
	}
    }
    
    public function webApplicationAttributes()
    {
	return [];
    }
    
    public function consoleApplicationAttributes()
    {
	return [];
    }
    
    public function aliases()
    {
	return [];
    }
    
    public function keyValueAttributes()
    {
	return [];
    }
    
    public function commonApplicationAttributes()
    {
	$attributes = $this->getAttributes();
	
	return [
	    'modules' => [
		'm0r1' => $attributes,
	    ]
	];
    }
}