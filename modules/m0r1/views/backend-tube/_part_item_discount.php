<div class="m0r1itemdiscount">
    <div>
	<select style="width:100%;">
	    <?php foreach( $discounttypes as $dt ): ?>
	    <option value="<?= $dt->id ?>"<?= ( isset( $params['disc_type_id'] ) && intval( $params['disc_type_id'] ) === $dt->id )?" selected=\"selected\"":"" ?>><?= $dt->name  ?></option>
	    <?php endforeach; ?>
	</select>
    </div>
    <div>
	<select style="width:100%;">
	    <?php foreach( $discounts as $disc ): ?>
	    <option value="<?= $disc->id ?>"<?= ( isset( $params['disc_id'] ) && intval( $params['disc_id'] ) === $disc->id )?" selected=\"selected\"":"" ?>><?= $disc->name.' | '.$disc->appliance.' | '.$disc->value.(($disc->value_in_percent)?"%":"")  ?></option>
	    <?php endforeach; ?>
	</select>
    </div>
    <div>
	<a class="btn btn-primary btn-sm"><i class="fa fa-save"></i></a>
    </div>
</div>