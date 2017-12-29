<div class="<?= $itemclasses ?>" data-id="<?= $item['id'] ?>">
    <div style="text-align:center;">
	<a target="_blank" href="<?= $item['url'] ?>"><?= $item['img'] ?></a>
    </div>
    <div style="text-align:center;">
	<a target="_blank" href="<?= $item['url'] ?>"><?= $item['name'] ?></a>
    </div>
    <div style="text-align:center;">
	<span style="font-size:0.9em;"><?= $item['price_p'] ?></span>
    </div>
    <div class="m0r1bbw-count-panel" data-total-count="<?= $item['total_count'] ?>" data-count="<?= isset($bonuses[$item['id']])?$bonuses[$item['id']]['count']:1 ?>">
	<div class="row">
	    <div class="col-md-4">
		<div><a><i class="fa fa-minus"></i></a></div>
	    </div>
	    <div class="col-md-4"><div><?= isset($bonuses[$item['id']])?$bonuses[$item['id']]['count']:1 ?></div></div>
	    <div class="col-md-4">
		<div><a><i class="fa fa-plus"></i></a></div>
	    </div>
	</div>
    </div>
    <div class="m0r1bbw-shop-panel m0r1bbw-shop-panel-used">
	<div>
	<a class="btn btn-default"><i class="fa fa-shopping-cart"></i><?= Yii::t('m0r1','M0r1BBW Bonuses Add To Order') ?></a>
	<a class="btn btn-default"><i class="fa fa-times"></i></a>
	</div>
        <div>
            <a class="btn btn-danger"><i class="fa fa-trash-o"></i><?= Yii::t('app','Delete') ?></a>
        </div>
    </div>
    <div class="clearfix"></div>
</div>