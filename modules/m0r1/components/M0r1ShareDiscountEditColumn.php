<?php

namespace app\modules\m0r1\components;

use Yii;
use yii\grid\DataColumn;

class M0r1ShareDiscountEditColumn extends DataColumn
{
    public $form = NULL;
    
    protected function renderDataCellContent($model, $key, $index)
    {
	return \app\modules\m0r1\widgets\ShareDiscountEPWidget::widget(['model'=>$model]);
    }
}
