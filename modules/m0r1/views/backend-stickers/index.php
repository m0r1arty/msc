<?php

use app\backend\components\ActiveForm;
use kartik\icons\Icon;
use kartik\helpers\Html;

$this->title = Yii::t('m0r1','M0r1 Order Stickers Backend Menu');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php $form = ActiveForm::begin(['id' => 'stickers-form', 'type' => ActiveForm::TYPE_HORIZONTAL]); ?>

<?php $this->beginBlock('submit'); ?>
<div class="form-group no-margin">
    <?=
    Html::a(
        Icon::show('arrow-circle-left') . Yii::t('app', 'Back'),
        Yii::$app->request->get('returnUrl', ['index']),
        ['class' => 'btn btn-danger']
    )
    ?>
    <?=Html::submitButton(
        Icon::show('save') . Yii::t('app', 'Save & Go back'),
        [
            'class' => 'btn btn-warning',
            'name' => 'action',
            'value' => 'back',
        ]
    );?>
    <?=
    Html::submitButton(
        Icon::show('save') . Yii::t('app', 'Save'),
        [
            'class' => 'btn btn-primary',
            'name' => 'action',
            'value' => 'save',
        ]
    )
    ?>
</div>
<?php $this->endBlock('submit'); ?>

<div id="actions">
    <?=
	\yii\helpers\Html::tag('span',
	    Icon::show('plus') . Yii::t('app', 'Add files..'),
	    [
		'class' => 'btn btn-success fileinput-button'
	    ]
	)?>
	<?php
	    if (Yii::$app->getModule('elfinder')) {
		echo \DotPlant\ElFinder\widgets\ElfinderFileInput::widget(
		    ['url' => Url::toRoute(['addImage', 'objId' => $object->id, 'objModelId' => $model->id])]
		);
	    }
	?>

</div>

<?=\app\modules\image\widgets\ImageDropzone::widget(
    [
	'name' => 'file',
	'url' => ['/shop/backend-product/upload'],
	'removeUrl' => ['/shop/backend-product/remove'],
	'uploadDir' => '/theme/resources/product-images',
	'sortable' => true,
	'sortableOptions' => [
	    'items' => '.dz-image-preview',
	],
	'objectId' => 4,
	'modelId' => 0,
	'htmlOptions' => [
	    'class' => 'table table-striped files',
	    'id' => 'previews',
	],
	'options' => [
	    'clickable' => ".fileinput-button",
	],
    ]
);?>
<?= $this->blocks['submit'] ?>
<?php ActiveForm::end(); ?>