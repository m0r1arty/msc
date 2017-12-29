<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

?>
<div style="width:100%;">
<select style="width:100%;" id="select2-cond-modal">
<?php foreach( $conditions as $cnd): ?>
<option value="<?= $cnd['id'] ?>"<?php echo ( isset($edit) && $cnd['selected'] )?" selected=\"selected\"":""; ?>><?= $cnd['name'] ?></option>
<?php endforeach; ?>
</select>
</div>
<div style="width:100%;">
<select style="width:100%;" id="select2-cond-item-modal">
<?= $this->render('_part_cond_items',ArrayHelper::merge(['cnd0items'=>$cnd0items], isset( $edit )?['edit' => $edit, 'citem' => $item ]:[] ) ) ?>
</select>
</div>
<div class="form-group m0r1-select-cond-val"<?= ( isset( $edit ) && $item['type'] === 'valCond' )?" style=\"display:block;\"":"" ?>>
    <input type="text" class="form-control" <?php if( isset( $edit ) && in_array( $item['type'], ['valCond','exprCond'] ) )
    {
	echo "value=\"".Html::encode($item['val'])."\"";
    } ?>/>
</div>
<div class="form-group m0r1-select-cond-expr"<?= ( isset( $edit ) && $item['type'] === 'exprCond' )?" style=\"display:block;\"":"" ?>>
    <div>
	<select>
	    <option value="<?= Html::encode("==") ?>"<?= ( isset( $edit ) && $item['type'] === 'exprCond' && $item['expr'] === "=="  )?" selected=\"selected\"":"" ?>><?= Html::encode("==") ?></option>
	    <option value="<?= Html::encode("<") ?>"<?= ( isset( $edit ) && $item['type'] === 'exprCond' && $item['expr'] === "<"  )?" selected=\"selected\"":"" ?>><?= Html::encode("<") ?></option>
	    <option value="<?= Html::encode("<=") ?>"<?= ( isset( $edit ) && $item['type'] === 'exprCond' && $item['expr'] === "<="  )?" selected=\"selected\"":"" ?>><?= Html::encode("<=") ?></option>
	    <option value="<?= Html::encode(">") ?>"<?= ( isset( $edit ) && $item['type'] === 'exprCond' && $item['expr'] === ">"  )?" selected=\"selected\"":"" ?>><?= Html::encode(">") ?></option>
	    <option value="<?= Html::encode(">=") ?>"<?= ( isset( $edit ) && $item['type'] === 'exprCond' && $item['expr'] === ">="  )?" selected=\"selected\"":"" ?>><?= Html::encode(">=") ?></option>
	</select>
    </div>
    <div>
	<input type="text" class="form-control" <?php if( isset( $edit ) && $item['type'] === 'exprCond' )
    {
	echo "value=\"".Html::encode($item['val'])."\"";
    } ?>/>
    </div>
</div>
<div class="m0r1-select-cond-act">
    <div>
	<select style="width: 100%;">
	    <option value="ret"<?= ( isset( $edit  ) && $item['act'] === 'ret' )?" selected=\"selected\"":""  ?>><?= Html::encode(Yii::t('m0r1','M0r1 FS System Tube Act|ret')) ?></option>
	    <option value="tube"<?= ( isset( $edit  ) && $item['act'] === 'tube' )?" selected=\"selected\"":""  ?>><?= Html::encode(Yii::t('m0r1','M0r1 FS System Tube Act|tube')) ?></option>
	    <option value="cont"<?= ( isset( $edit  ) && $item['act'] === 'cont' )?" selected=\"selected\"":""  ?>><?= Html::encode(Yii::t('m0r1','M0r1 FS System Tube Act|cont')) ?></option>
	</select>
    </div>
    <div<?= ( isset( $edit ) && $item['act'] === 'tube' )?" style=\"display:block;\"":""; ?>>
	<select style="width: 100%;">
	    <?php foreach( $tubes as $tube): ?>
		<option value="<?= $tube->id ?>"<?= ( isset( $edit ) && $item['act'] === 'tube' && $item['tube_id'] == $tube->id )?" selected=\"selected\"":"" ?>><?= Html::encode($tube->name) ?></option>
	    <?php endforeach; ?>
	</select>
    </div>
    <div>
	<a class="btn btn-success btn-sm"><i class="fa fa-plus" style="margin-right:8px;"></i></a>
    </div>
</div>
