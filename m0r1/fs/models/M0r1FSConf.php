<?php

namespace app\m0r1\fs\models;

use Yii;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

use app\m0r1\fs\models\M0r1FSTube;
use app\m0r1\fs\models\M0r1FSStrategy;

class M0r1FSConf extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_fs_conf}}';
    }
    
    public function rules()
    {
	return [
	    [['name','tube_id','strategy_id'],'required'],
	    [['name'],'string','max'=>255],
	    [['tube_id','strategy_id'],'integer'],
	];
    }
    
    public function scenarios()
    {
	return [
	    'default'	=> ['name','tube_id','strategy_id'],
	    'search'	=> ['name'],
	];
    }
    
    public function attributeLabels()
    {
	return [
	    'id'		=> Yii::t('app','ID'),
	    'name'		=> Yii::t('app','Name'),
	    'tube_id'		=> Yii::t('m0r1','M0r1 FS System Tube Tube ID'),
	    'strategy_id'	=> Yii::t('m0r1','M0r1 FS System Tube Strategy ID'),
	];
    }
    
    public function getAllTubes()
    {
	return ArrayHelper::map(M0r1FSTube::find()->all(),'id','name');
    }

    public function getAllStrategies()
    {
	return ArrayHelper::map(M0r1FSStrategy::find()->all(),'id','name');
    }
    
    public function getTube()
    {
	return $this->hasOne(M0r1FSTube::className(),['id'=>'tube_id']);
    }
    
    public function getStrategy()
    {
	return $this->hasOne(M0r1FSStrategy::className(),['id'=>'strategy_id']);
    }
}
