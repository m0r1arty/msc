<?php

use yii\helpers\ArrayHelper;

use app\backend\widgets\BackendWidget;

?>

<div id="m0r1-settings">
    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title' => Yii::t('m0r1','M0r1 Settings Configurable Title Export'),
		'options' => ['class'=>'visible-header'],
	    ]);
	    echo $form->field($model,'exportCurrencyID')->dropDownList( $model->getCurrenciesArray() );
	    echo $form->field($model,'exportKeyPropertyID')->dropDownList( $model->getPropertiesArray()  );
	?>
	<?php
	    BackendWidget::end();
	?>
    </div>
    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title' => Yii::t('m0r1','M0r1 Settings Configurable Property Image Size Title'),
		'options' => ['class'=>'visible-header'],
	    ]);
	    
	    echo $form->field($model,'imgPropertySizeID')->dropDownList( $model->getSizesIDs() );
	    ?>
	<?php
	    BackendWidget::end();
	?>
    </div>
    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title' => Yii::t('m0r1','M0r1 Finance System Backend Menu Full Title'),
		'options' => ['class'=>'visible-header'],
	    ]);
	    
	    echo $form->field($model,'fsConfID')->dropDownList( $model->getAllFSConfs() );
	    
	    ?>
	<?php
	    BackendWidget::end();
	?>
    </div>

    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title'		=> Yii::t('m0r1','M0r1 Settings Configurable Bonuses Title'),
		'options'	=> ['class'=>'visible-header'],
	    ]);
	    echo $form->field( $model,'bonusCurrencyID')->dropDownList( $model->getCurrenciesArray() );
	    echo $form->field( $model,'bonusCategoryID')->dropDownList( $model->getCategoriesArray() );
	    echo $form->field( $model,'bonusesPerRow' )->textInput( ['maxlength'=>10] );
	    echo $form->field( $model,'bonusesPerPage' )->textInput( ['maxlength'=>10] );
	    echo $form->field( $model,'bonusesItemClasses' )->textInput();
	    echo $form->field( $model, 'bonusesThumbSizeID')->dropDownList( $model->getSizesIDs() );
	    echo $form->field( $model, 'bonusesImgNotFound' )->textInput( ['maxlength' => 1000] );
	    BackendWidget::end();
	?>
    </div>

    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title'		=> Yii::t('m0r1','M0r1 Accumulative Discount'),
		'options'	=> ['class'=>'visible-header'],
	    ]);
	    echo $form->field( $model, 'accDiscountStageId')->dropDownList( $model->getAccStages() );
	    echo $form->field( $model, 'accStrategy' )->dropDownList( $model->getAccStrategies() );
	    BackendWidget::end();
	?>
    </div>

    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title'		=> Yii::t('m0r1','M0r1 Order Stickers Backend Menu'),
		'options'	=> ['class'=>'visible-header'],
	    ]);
	    echo $form->field( $model, 'stickerThumbSizeID' )->dropDownList( $model->getSizesIDs() );
	    BackendWidget::end();
	?>
    </div>

    <div class="col-md-6 col-sm-12">
	<?php
	    BackendWidget::begin([
		'title'		=> Yii::t('m0r1','M0r1 Magic Property'),
		'options'	=> ['class'=>'visible-header'],
	    ]);
	    echo $form->field( $model, 'magicTubeID' )->dropDownList( $model->getTubeIDs() );
	    echo $form->field( $model, 'magicPropertyID' )->dropDownList( $model->getPropertiesArray() );
	    	    echo $form->field( $model, 'dummyStrategyID' )->dropDownList( ArrayHelper::map( \app\m0r1\fs\models\M0r1FSStrategy::find()->orderBy(['name'=>SORT_ASC])->all(), 'id', 'name' ) );
	    BackendWidget::end();
	?>
    </div>
</div>