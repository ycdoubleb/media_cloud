<?php

use yii\grid\GridView;

/**
 * 素材收入金额子页面
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
                    'style' => 'width: 35px',
                ],
            ],
            [
                'attribute' => 'id',
                'label' => Yii::t('app', 'Media Sn'),
                'headerOptions' => [
                    'style' => 'width: 75px',
                ],
            ],
            [
                'attribute' => 'name',
                'label' => Yii::t('app', '{Media}{Name}', [
                    'Media' => Yii::t('app', 'Media'),
                    'Name' => Yii::t('app', 'Name'),
                ]),
            ],
            [
                'attribute' => 'nickname',
                'label' => Yii::t('app', 'Operator'),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
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
        ],
    ]); ?>
</div>