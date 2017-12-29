<?php

use \yii\helpers\Html;

?>
<div class="jarviswidget" data-id="<?= $tiid ?>" data-pos="<?= $pos ?>" draggable="true">
    <header>
	
	<a class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>
	<a class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></a>
	
	<h2><i class="fa fa-gear" style="margin-right:8px;"></i><?= '['.$cond_name.'|'.$item.']' ?></h2>
	<div></div>
    </header>
    <div class="well well-lg">
	<?= Yii::t('m0r1','M0r1 FS System Tube Cond View|Provider') ?><?= $cond_name ?> , <?= Yii::t('m0r1','M0r1 FS System Tube Cond View|Item')?><?= Yii::t('m0r1','M0r1 FS System Tube Cond '.$cond_name.'|'.$item) ?> , <?= Yii::t('m0r1','M0r1 FS System Tube Cond View|Type') ?><?= Yii::t('m0r1','M0r1 FS System Tube Cond View|'.$type)  ?> ,
	<?php
	    if( $type == 'valCond' || $type == 'exprCond' )
	    {
		if( $type == 'exprCond' )
		{
		    echo Yii::t('m0r1','M0r1 FS System Tube Cond View|Expr').$expr." , ";
		}
		
		echo Yii::t('m0r1','M0r1 FS System Tube Cond View|Val').Html::encode($val)." , ";
	    }
	?>
	<?= Yii::t('m0r1','M0r1 FS System Tube Cond View|Act') ?><?= Yii::t('m0r1','M0r1 FS System Tube Cond View|act'.$act)  ?>
	<?php
	    
	    if ( $act === 'tube' )
	    {
		echo " , ".Yii::t('m0r1','M0r1 FS System Tube Cond View|Tube').$tube;
	    }
	?>
    </div>
</div>