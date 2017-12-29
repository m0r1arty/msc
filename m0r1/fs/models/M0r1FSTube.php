<?php

namespace app\m0r1\fs\models;

use Yii;

use yii\db\ActiveRecord;


class M0r1FSTube extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_fs_tube}}';
    }
    
    public function rules()
    {
	return [
	    [['name'],'required'],
	    [['name'],'string','max'=>255],
	    [['name'],'unique'],
	];
    }
    
    public function scenarios()
    {
	return [
	    'default'	=> ['name'],
	    'search'	=> ['name'],
	];
    }
    
    public function attributeLabels()
    {
	return [
	    'id'	=> Yii::t('app','ID'),
	    'name'	=> Yii::t('app','Name'),
	];
    }
}