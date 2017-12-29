<?php
use yii\helpers\ArrayHelper;
?>
<?php foreach( $cnd0items as $item): ?>
<option value="<?= $item['name'] ?>" data-type="<?= $item['type'] ?>"<?php echo ( isset( $edit ) && $item['name'] === $citem['item'])?" selected=\"selected\"":""; ?>><?= $item['description']?></option>
<?php endforeach; ?>