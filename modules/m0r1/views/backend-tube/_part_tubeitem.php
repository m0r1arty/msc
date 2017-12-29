<div draggable="true" class="jarviswidget" data-id="<?= $id ?>" data-pos="<?= $pos ?>">
    <header><a class="btn btn-danger btn-sm m0r1itemdel"><i class="fa fa-trash-o"></i></a><h2><i class="fa fa-gear" style="margin-right:8px;"></i><?= $name ?></h2><div></div></header>
    <div>
	<?= $view ?>
    </div>
    <div class="m0r1conditions">
	<div><a class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right:8px;"></i><?= Yii::t('m0r1','M0r1 FS System Add Condition') ?></a></div>
	<?php foreach( $conditions as $cond): ?>
	<div><?= $cond ?></div>
	<?php endforeach; ?>
    </div>
</div>