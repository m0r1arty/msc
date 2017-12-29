<?php

use yii\helpers\ArrayHelper;

use app\backend\widgets\BackendWidget;
use \kartik\widgets\Select2;
use app\modules\m0r1\models\M0r1Order2Sticker;

?>
<div class="col-xs-12 col-md-6">
<?php BackendWidget::begin([
	'icon'	=> 'picture-o',
	'title'	=> Yii::t('m0r1','M0r1 Order Sticker'),
]); ?>
<?= $form->field($model,'sticker_id')->widget(Select2::className(),[
    'name'	=> 'order-sticker',
    'data'	=> ArrayHelper::map( M0r1Order2Sticker::getStickers(), 'id', 'value' ),
    'pluginOptions'	=> [
	'allowClear'	=> true,
	'placeholder'	=> Yii::t('m0r1','M0r1 Order Stickers Select Placeholder'),
    ],
])  ?>
<?php BackendWidget::end(); ?>
</div>