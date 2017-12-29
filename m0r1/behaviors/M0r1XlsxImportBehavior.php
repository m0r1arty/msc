<?php

namespace app\m0r1\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\Expression;

use app\models\Object;
use app\models\ObjectPropertyGroup;
use app\models\ObjectStaticValues;
use app\models\Property;
use app\models\PropertyGroup;
use app\models\PropertyStaticValues;
use app\modules\shop\models\Product;

use app\modules\data\components\ImportableInterface;

class M0r1XlsxImportBehavior extends Behavior
{
    public function m0r1ProcessExport($exportFields = [], $conditions = [], $batchSize = 100)
    {
	if( $this->owner->object->name !== 'Product' )
	{
	    $this->owner->_exportRoutine = 'processExport';
	    return $this->owner->processExportInvk($exportFields,$conditions,$batchSize);
	}

	$fields = $this->owner->getAllFields($exportFields);
	$class = $this->owner->object->object_class;
	
	$select = $class::find();
	
        $representationConversions = [
    	    'text' => 'name',
            'value' => 'value',
            'id' => 'psv_id',
        ];
        
        $product = Yii::$container->get(Product::class);
        
        if (
    	    isset($conditions['category']) &&
    	    is_array($conditions['category']) &&
    	    $this->owner->object->id == Object::getForClass(get_class($product))->id
    	) {
    	    foreach ($conditions['category'] as $condition) {
    		$joinTableName = 'Category'.$condition['value'];
    		
    		$select->innerJoin(
    		    "{{%product_category}} " . $joinTableName,
    		    "$joinTableName.object_model_id = product.id"
    		);
    		
    		$select->andWhere(
    		    new Expression(
    			'`' . $joinTableName . '`.`category_id` = "'.$condition['value'].'"'
    		    )
    		);
    	    }
    	}
    	
    	if (isset($conditions['field']) && is_array($conditions['field'])) {
    	    foreach ($conditions['field'] as $condition) {
    		$conditionOptions = [$condition['operators'], $condition['value'], $condition['option']];
    		
    		if ($condition['comparison'] == 'AND') {
    		    $select->andWhere($conditionOptions);
    		} elseif ($condition['comparison'] == 'OR') {
    		    $select->orWhere($conditionOptions);
    		}
    	    }
    	}
    	
    	if (isset($conditions['property']) && is_array($conditions['property'])) {
    	    foreach ($conditions['property'] as $condition) {
    		$property = Property::findById($condition['value']);
    		
    		if ($property && isset($condition['option']) &&  !empty($condition['option'])) {
    		    if ($property->is_eav) {
    		    
    			$joinTableName = 'EAVJoinTable'.$property->id;
    			
    			$select->innerJoin(
    			    $this->owner->object->eav_table_name . " " . $joinTableName,
    			    "$joinTableName.object_model_id = " .
    			    Yii::$app->db->quoteTableName($this->owner->object->object_table_name) . ".id "
    			);
    			
    			$select->andWhere(
    			    new Expression(
    				'`' . $joinTableName . '`.`value` '.$condition['operators'].' "'.$condition['option'].'" AND `' .
    				$joinTableName . '`.`key` = "'. $property->key.'"'
    			    )
    			);
    			
    		    } elseif ($property->has_static_values) {
    			
    			$joinTableName = 'OSVJoinTable'.$property->id;
    			$propertyStaticValue = PropertyStaticValues::find()->where(['value'=>$condition['option']])->one();
    			
    			if ($propertyStaticValue) {
    			    $select->innerJoin(
    				ObjectStaticValues::tableName() . " " . $joinTableName,
    				"$joinTableName.object_id = " . intval($this->owner->object->id) .
    				" AND $joinTableName.object_model_id = " .
    				Yii::$app->db->quoteTableName($this->owner->object->object_table_name) . ".id "
    			    );
    			    
    			    $select->andWhere(
    				new Expression(
    				    '`' . $joinTableName . '`.`property_static_value_id` ="'.$propertyStaticValue->id.'"'
    				)
    			    );
    			}
    			
    		    } else {
    			throw new \Exception("Wrong property type for ".$property->id);
    		    }
    		}
    	    }
    	}
    	
    	$data = [];
    	
    	$batchSize = intval($batchSize) <= 0 ? 100 : intval($batchSize);
    	
    	foreach ($select->each($batchSize) as $object) {
    	    
    	    $row = [];
    	    
    	    foreach ($fields['fields_object'] as $field) {
    		if ('internal_id' === $field && $this->owner->_processInternalId) {
    		    $row[] = $object->id;
    		} else {
    		    $row["F|".$field] = isset($object->$field) ? $object->$field : '';
    		}
    	    }
    	    
    	    foreach ($fields['fields_property'] as $field_id => $field) {
    		$value = $object->getPropertyValuesByPropertyId($field_id);
    		
    		if (!is_object($value)) {
    		    $value = '';
    		} elseif (count($value->values) > 1 && is_array($fields['fields_property'][$field_id]) && isset($fields['fields_property'][$field_id]['processValuesAs'])) {
    		    $attributeToGet = $representationConversions[$fields['fields_property'][$field_id]['processValuesAs']];
    		    $newValues = [];
    		    
    		    foreach ($value->values as $val) {
    			$newValues[] = $val[$attributeToGet];
    		    }
    		    
    		    $value = implode($this->owner->multipleValuesDelimiter, $newValues);
    		    
    		}elseif( isset($field['processValuesAs']) && count($value->values) == 1 ){
    		    list($value) = array_values($value->values);
    		    $attributeToGet = $representationConversions[$field['processValuesAs']];
    		    $value = $value[$attributeToGet];
    		} else {
    		    $value = (string) $value;
    		}
    		
    		$row["P|".$field_id] = $value;
    	    }
    	    
    	    if (!empty($fields['fields_additional']) && $object->hasMethod('getAdditionalFields')) {
    		$fieldsFromModel = $object->getAdditionalFields($fields['fields_additional']);
    		
    		foreach ($fields['fields_additional'] as $key => $configuration) {
    		    if (!isset($fieldsFromModel[$key])) {
    			$fieldsFromModel[$key] = '';
    		    }
    		    
    		    if (!empty($fieldsFromModel[$key])) {
    			$value = (array)$fieldsFromModel[$key];
    			$row["AF|".$key] = implode($this->owner->multipleValuesDelimiter, $value);
    		    } else {
    			$row["AF|".$key] = '';
    		    }
    		}
    	    }
    	    
    	    if( !isset( $headerSorted ) )
    	    {
    		$headerSorted = [];
    		
    		foreach( $row as $rk => $rv )
    		{
    		    $i = 0;
    		    
    		    foreach( $exportFields['m0r1Sorting'] as $rvv)
    		    {
    			if( $rk === $rvv )
    			{
    			    $headerSorted[] = $i;
    			    break;
    			}
    			$i++;
    		    }
    		}
    	    }
    	    
    	    $rowSorted = [];
    	    
    	    foreach($exportFields['m0r1Sorting'] as $rk )
    	    {
    		$rowSorted[] = $row[$rk];
    	    }
    	    
    	    $data[] = $rowSorted;
    	}
    	
    	unset($value, $row, $object, $select, $class);
    	
    	
    	$header = [];
    	
    	foreach( $headerSorted as $rk )
    	{
    	    $header[] = $fields['fields_header'][$rk];
    	}
    	
    	return $this->owner->getData($header, $data);
    	
    }
    
    public function m0r1ProcessImport($importFields = [])
    {
	if( $this->owner->object->name !== 'Product' )
	{
	    $this->owner->_importRoutine = 'processImport';
	    return $this->owner->processImportInvk($importFields);
	}
	
	$mk = Property::find()->select(['key'])->where(['id' => Yii::$app->getModule('m0r1')->exportKeyPropertyID ])->scalar();
	$tbl = $this->owner->object->column_properties_table_name;

	$this->owner->_dataHeaderProcessor = ['app\m0r1\components\PrettyXlsxDHProcessor','processReverseHeader'];
	$fields = $this->owner->getAllFields($importFields);
	$data = $this->owner->setData();
	
	$objectFields = $this->owner->getFields($this->owner->object->id);
	$objAttributes = $objectFields['object'];
	$propAttributes = isset($objectFields['property']) ? $objectFields['property'] : [];
	$objPropKeys = [];
	
	foreach( $importFields['property'] as $k => $v )
	{
	    $objPropKeys[$k] = $v['key'];
	}
	
	$titleFields = array_filter(
	    array_shift($data),
	    function ($value) {
		return !empty($value);
	    }
	);

	$titleFields = [];
	
	$transaction = \Yii::$app->db->beginTransaction();

	try {
	    
	    $objDataFields = [];
	    $objPropFields = [];
	    
	    $i = 0;
	    
	    foreach( $importFields['m0r1Sorting'] as $ms )
	    {
		$tmp = explode('|',$ms);
		
		if( $tmp[0] == 'F' )
		{
		    $objDataFields[$tmp[1]] = $i;
		    $titleFields[ $tmp[1] ] = $tmp[1];
		}elseif( $tmp[0] == 'P' )
		{
		    $objPropFields[ $objPropKeys[ $tmp[1] ] ] = $i;
		    $titleFields[$objPropKeys[ $tmp[1] ] ] = $objPropKeys[ $tmp[1] ];
		}
		
		$i++;
	    }
	    $columnsCount = count($titleFields);

	    foreach ($data as $row) {
		
		$objData = [];
		$propData = [];
		
		foreach ( $objDataFields as $k => $v ) {
		    $objData[$k] = $row[ $v ];
		}

		foreach ( $objPropFields as $k => $v ) {

		    $propValue = $row[ $v ];
		    
		    if (!empty($this->owner->multipleValuesDelimiter)) {
		    
			if (strpos($propValue, $this->owner->multipleValuesDelimiter) > 0) {
			    $values = explode($this->owner->multipleValuesDelimiter, $propValue);
			} elseif (strpos($this->owner->multipleValuesDelimiter, '/') === 0) {
			    $values = preg_split($this->owner->multipleValuesDelimiter, $propValue);
			} else {
			    $values = [$propValue];
			}
			
			$propValue = [];
			
			foreach ($values as $value) {
			    $value = trim($value);
			    if (!empty($value)) {
				$propValue[] = $value;
			    }
			}
		    }
		    
		    $propData[ $k ] = $propValue;
		}
		
		
		$objectId = (new \yii\db\Query())->select('object_model_id')->from($tbl)->where([$mk=>$row[0]])->scalar();
		
		if( $objectId === FALSE)
		{
		    $objectId = 0;
		}
		
		$this->owner->saveInvk($objectId,$objData,$fields['fields_object'],$propData,$fields['fields_property'],$row,$titleFields,$columnsCount);
	    }
	    
	} catch (\Exception $exception) {
	    $transaction->rollBack();
	    throw $exception;
	}
	
	$transaction->commit();
	
	return true;
    }
    
    public function m0r1Save($objectId, $object, $objectFields = [], $properties = [], $propertiesFields = [], $row=[], $titleFields=[], $columnsCount = null)
    {
	if( $this->owner->object->name !== 'Product' )
	{
	    $this->owner->_saveRoutine = 'save';
	    return $this->owner->saveInvk($objectId,$object,$objectFields,$properties,$propertiesFields,$row,$titleFields,$columnsCount);
	}
	
	if ($columnsCount === null) {
	    $columnsCount = count($titleFields);
	}
	
	try {
	    $rowFields = array_combine(array_keys($titleFields), array_slice($row, 0, $columnsCount));
	} catch(\Exception $e) {
	    echo "title fields: ";
	    var_dump(array_keys($titleFields));
	    echo "\n\nRow:";
	    var_dump($row);
	    echo "\n\n";
	    throw $e;
	}
	
	$class = $this->owner->object->object_class;
	
	if ($objectId > 0) {
	    $objectModel = $class::findOne($objectId);
	    
	    if (!is_object($objectModel)) {
		if ($this->owner->createIfNotExists === true) {
		    $objectModel = new $class;
		    $objectModel->id = $objectId;
		} else {
		    return;
		}
	    }
	    
	    $objectData = [];
	    
	    foreach ($objectFields as $field) {
		if (isset($object[$field])) {
		    $objectData[$field] = $object[$field];
		}
	    }
	} else {
	    $objectModel = new $class;
	    $objectModel->loadDefaultValues();
	    $objectData = $object;
	}
	
	if ($objectModel) {
	    
	    if( $objectId == 0 )
	    {
		$rowFields['categories'] = '1';//force set main category "Catalog"
		$objectModel->main_category_id = 1;
	    }
	    
	    $objectModel->currency_id = Yii::$app->getModule('m0r1')->exportCurrencyID;

	    if ($objectModel instanceof ImportableInterface) {
		$objectModel->processImportBeforeSave($rowFields, $this->owner->multipleValuesDelimiter, $this->owner->additionalFields);
	    }
	    
	    if ($objectModel->save()) {
		
		if (!is_array($this->owner->addPropertyGroups)) {
		    $this->owner->addPropertyGroups = [];
		}
		
		foreach ($this->owner->addPropertyGroups as $propertyGroupId) {
		    $model = new ObjectPropertyGroup();
		    $model->object_id = $this->owner->object->id;
		    $model->object_model_id = $objectModel->id;
		    $model->property_group_id = $propertyGroupId;
		    $model->save();
		}
		
		if (count($this->owner->addPropertyGroups) > 0) {
		    $objectModel->updatePropertyGroupsInformation();
		}
		
		$propertiesData = [];
		$objectModel->getPropertyGroups();
		
		foreach ($propertiesFields as $propertyId => $field) {
		    
		    if (isset($properties[$field['key']])) {
			
			$value = $properties[$field['key']];
			
			if (isset($field['processValuesAs'])) {
			    
			    $staticValues = PropertyStaticValues::getValuesForPropertyId($propertyId);
			    
			    $representationConversions = [
				    'text' => 'name',
				    'value' => 'value',
				    'id' => 'id',
				];
			    
			    $attributeToGet = $representationConversions[$field['processValuesAs']];
			    $ids = [];
			    
			    foreach ($value as $initial) {
				$original = $initial;
				$initial = mb_strtolower(trim($original));
				$added = false;
				
				foreach ($staticValues as $static) {
				    if (mb_strtolower(trim($static[$attributeToGet])) === $initial) {
					$ids [] = $static['id'];
					$added = true;
				    }
				}
				
				if (!$added) {
				    $model = new PropertyStaticValues();
				    $model->property_id = $propertyId;
				    $model->name = $model->value = $model->slug = $original;
				    $model->sort_order = 0;
				    $model->title_append = '';
				    
				    if ($model->save()) {
					$ids[] = $model->id;
				    }
				    
				    unset(PropertyStaticValues::$identity_map_by_property_id[$propertyId]);
				    
				    \yii\caching\TagDependency::invalidate(
					Yii::$app->cache,
					[
					    \devgroup\TagDependencyHelper\ActiveRecordHelper::getObjectTag(Property::className(), $propertyId)
					]
				    );
				}
			    }
			    
			    $value = $ids;
			}
			
			$propertiesData[$field['key']] = $value;
		    }
		}
		
		if (!empty($propertiesData)) {
		    $objectModel->saveProperties(
			[
			    "Properties_{$objectModel->formName()}_{$objectModel->id}" => $propertiesData
			]
		    );
		}
		
		if ($objectModel instanceof ImportableInterface) {
		    $objectModel->processImportAfterSave($rowFields, $this->owner->multipleValuesDelimiter, $this->owner->additionalFields);
		}
		
		if ($objectModel->hasMethod('invalidateTags')) {
		    $objectModel->invalidateTags();
		}
		
	    } else {
		throw new \Exception('Cannot save object: ' . var_export($objectModel->errors, true) . var_export($objectData, true) . var_export($objectModel->getAttributes(), true));
	    }
	}
    }
}
