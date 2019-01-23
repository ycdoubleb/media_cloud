<?php

use yii\grid\GridView;

/**
 * 运营人收入金额子页面
 * 左侧表格部分
 */

?>
<div class="meida-table">
    <?= GridView::widget([
        'dataProvider' => $listsData,
        'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => 'width: 50px',
                ],
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('app', 'Operator'),
            ],
            [
                'attribute' => 'value',
                'label' => Yii::t('app', '{Income}{Amount}',[
                    'Income' => Yii::t('app', 'Income'),
                    'Amount' => Yii::t('app', 'Amount')
                ]),
                'format' => 'raw',
                'value' => function($data) {
                    return Yii::$app->formatter->asCurrency($data['value']);
                },
            ],
            [
                'attribute' => 'proportion',
                'label' => Yii::t('app', 'Proportion'),
                'format' => 'raw',
                'value' => function($data) {
                    return Yii::$app->formatter->asPercent($data['proportion'], 2);
                },
            ],
        ],
    ]); ?>
</div>