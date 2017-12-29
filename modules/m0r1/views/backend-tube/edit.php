<?php

/**
 * @var $this yii\web\View
 * @var $model \app\m0r1\fs\models\M0r1Tube
 */

use app\backend\widgets\BackendWidget;
use kartik\helpers\Html;
use kartik\icons\Icon;
use kartik\widgets\ActiveForm;

use yii\bootstrap\Modal;

\kartik\select2\Select2Asset::register($this);
\kartik\select2\ThemeDefaultAsset::register($this);

$this->title = $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update');
$this->params['breadcrumbs'][] = 
    ['label' => Yii::t('m0r1', 'M0r1 FS System Tube'), 'url' => ['index']];

$this->params['breadcrumbs'][] = $this->title;

$modal = new Modal([
	'header'	=> Yii::t('m0r1','M0r1 FS System Select Condition'),
]);

$id = $modal->getId();

$this->registerJs(<<<JS
    
    g_M0r1Item.setModalId('{$id}');
    
JS
,\yii\web\View::POS_END);


$modal->run();
?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php $form = ActiveForm::begin(); ?>
        <?php
            BackendWidget::begin(
                [
                    'icon' => 'car',
                    'title'=> Yii::t('m0r1', 'M0r1 FS System Tube'),
                    'footer' => Html::a(
                            Icon::show('arrow-circle-left') . Yii::t('app', 'Back'),
                            Yii::$app->request->get('returnUrl', ['index', 'id' => $model->id]),
                            ['class' => 'btn btn-danger']
                        ).' '.($model->isNewRecord ? (Html::submitButton(
                            Icon::show('save') . Yii::t('app', 'Save & Go next'),
                            [
                                'class' => 'btn btn-success',
                                'name' => 'action',
                                'value' => 'next',
                            ])):'').' '.(Html::submitButton(
                            Icon::show('save') . Yii::t('app', 'Save & Go back'),
                            [
                                'class' => 'btn btn-warning',
                                'name' => 'action',
                                'value' => 'back',
                            ]
                        )).' '.(Html::submitButton(
                            Icon::show('save') . Yii::t('app', 'Save'),
                            [
                                'class' => 'btn btn-primary',
                                'name' => 'action',
                                'value' => 'save',
                            ]
                        )),
                ]
            );
        ?>
<?php
        echo  $form->field($model, 'name')->textInput(['maxlength' => 255]);
?>
        <?php BackendWidget::end(); ?>
    <?php ActiveForm::end(); ?>
</div>
<?php if( !$model->isNewRecord ): ?>
<!-- Здесь у нас список с итемами Фин.Системы -->

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <div class="jarviswidget">
	<header>
	    <h2><i class="fa fa-gear" style="margin-right:8px;"></i><?= Yii::t('m0r1','M0r1 FS System Items') ?></h2>
	</header>
	<?php foreach( $items as $ti ): ?>
	<div class="m0r1item">
	    <div class="jarviswidget">
		<header><h2><i style="margin-right:8px;" class="fa fa-car"></i><?= $ti->name ?></h2></header>
		<div>
		    <pre><?= $ti->comment ?></pre>
		</div>
		<div>
		    <?php foreach( $ti->items as $item ): ?>
			<div class="m0r1item m0r1trueitem" draggable="true" data-item-id="<?= $item['id'] ?>">
			    <div class="jarviswidget">
				<header><h2><i class="fa fa-gear" style="margin-right:8px;"></i><?= $item['name']?></h2></header>
				<div>
				    <pre><?= $item['comment'] ?></pre>
				</div>
			    </div>
			</div>
		    <?php endforeach; ?>
		</div>
	    </div>
	</div>
	<?php endforeach; ?>
    </div>
</div>

<!-- А вот тут уже пошли рабочие итемы трубы с данными -->

<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <div class="jarviswidget">
	<header>
	    <h2><i class="fa fa-gear" style="margin-right:8px;"></i><?= Yii::t('m0r1','M0r1 FS System Items Tube') ?></h2>
	</header>
	<div class="m0r1itemstube">
	    <?php foreach( $tubeitems as $ti ): ?>
	    <?= $ti ?>
	    <?php endforeach; ?>
	</div>
    </div>
</div>
<?php endif; ?>














