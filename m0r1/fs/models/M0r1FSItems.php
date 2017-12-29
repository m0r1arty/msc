<?php

namespace app\m0r1\fs\models;

use Yii;
use ReflectionClass;

use yii\db\ActiveRecord;

use app\m0r1\fs\helpers\ItemHelper;

class M0r1FSItems extends ActiveRecord
{
    public static function tableName()
    {
	return '{{%m0r1_fs_items}}';
    }
    
    public function getComment()
    {
	$class = new ReflectionClass( $this->class );
	return $class->getDocComment();
    }
    
    public function getItems()
    {
	return ItemHelper::getItems($this->class,$this->name);
    }
    
    public function getItem( $name = null )
    {
	return ItemHelper::getItem( $this->class, $this->name, $name );
    }
}