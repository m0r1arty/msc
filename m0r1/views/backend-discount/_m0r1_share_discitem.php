<?php

use app\backend\widgets\BackendWidget;
?>

                    <?php BackendWidget::begin([
                        'title' => Yii::t('app', $object->type->name),
                        'icon' => 'cog',
                        'footer' => $this->blocks['submit']
                    ]); ?>
                    <?= \app\backend\widgets\GridView::widget(
                        [
                            'dataProvider' => $object::searchDiscountFilter($model->id),
                            'columns' => [
                                'fullName',
                                [
                            	    'class' => \app\modules\m0r1\components\M0r1ShareDiscountEditColumn::className(),
                            	    'form' => $form,
                                ],
                                [
                                    'class' => 'app\backend\components\ActionColumn',
                                    'buttons' => [
                                        [
                                            'url' => 'delete-filters',
                                            'icon' => 'trash-o',
                                            'class' => 'btn-danger',
                                            'label' => Yii::t('app', 'Delete'),
                                            'options' => [
                                                'data-action' => 'delete',
                                            ],
                                        ]
                                    ],
                                    'url_append' => '&typeId=' . $object->type->id
                                ],
                            ]
                        ]
                    );
                    ?>

                    <?= $this->render($object->type->add_view, ['form' => $form, 'object' => $object]) ?>
                    <?php BackendWidget::end(); ?>