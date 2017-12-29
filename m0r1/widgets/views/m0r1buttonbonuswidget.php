<?php

use yii\bootstrap\Modal;

Modal::begin([
	'size'		=> Modal::SIZE_LARGE,
	'header'	=> Yii::t('m0r1','M0r1BBW Bonuses Modal Header'),
]);
?>
    <div id="m0r1-bbw-flash-<?= $modalid ?>">
    </div>
    <div id="m0r1-bbw-body-<?= $modalid ?>">
    </div>
    <div id="m0r1-bbw-footer-<?= $modalid ?>" style="margin-top:20px;">
	<div style="display:inline;"><a class="btn btn-default"><i class="fa fa-arrow-left"></i></a></div>
	<div style="display:inline;">info</div>
	<div style="display:inline;"><a class="btn btn-default"><i class="fa fa-arrow-right"></i></a></div>
    </div>
<?php
Modal::end();
?>
<a id="m0r1bbwid" class="btn btn-success"><?= Yii::t('m0r1','M0r1BBW {countbonuses} bonuses',['countbonuses'=> $isAdmin? 'XXXXXX' : $bonuses->format($bprice)]) ?> </a>