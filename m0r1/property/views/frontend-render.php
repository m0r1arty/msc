<?php

/**
 * @var $attribute_name string
 * @var $form \yii\widgets\ActiveForm
 * @var $label string
 * @var $model \app\properties\AbstractModel
 * @var $multiple boolean
 * @var $property_id integer
 * @var $property_key string
 * @var $this \app\properties\handlers\Handler
 * @var $values array
 */

use app\models\Property;
use app\modules\shop\helpers\CurrencyHelper;
use kartik\helpers\Html;

use app\m0r1\fs\helpers\StrgHlp;

$prop = Property::findOne( Yii::$app->getModule('m0r1')->magicPropertyID );

$count = intval( $model->getPropertyValueByAttribute( $prop->key )->toValue() );

$product_id = $model->getOwnerModel()->id;

$calc_price = StrgHlp::getDummyPrice( [ $product_id ] );

$product_price = CurrencyHelper::getMainCurrency()->format( ( $calc_price / $count ) );

?>
    <?php
        $property = Property::findById($property_id);
        $result = "";
        $valuesRendered = 1;
        $result .= '<meta itemprop="main" content="True"/>';
        $result .= Html::tag(
            'dd',
             Html::encode(
				$product_price
             ),
                        [
                            'itemprop' => 'value',
                        ]
        );
                
            
        
        $result = trim($result);

        if (!empty($result)) {
            echo '<dl itemprop="itemListElement" itemscope itemtype="http://schema.org/NameValueStructure">' .
                Html::tag('dt', $property->name, ['itemprop'=>'name']) .
                $result . "</dl>\n\n";
        }
    ?>
