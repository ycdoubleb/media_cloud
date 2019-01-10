<?php

use common\components\aliyuncs\Aliyun;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\Html;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="meida-table">
    <?= GridView::widget([
        'dataProvider' => $resourcesData,
        'tableOptions' => ['class' => 'table table-bordered mc-table'],
        'layout' => "{items}\n{pager}",
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => 'width: 40px',
                ],
            ],
            [
                'attribute' => 'media_id',
                'label' => Yii::t('app', 'Resources Sn'),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
            ],
            [
                'attribute' => 'cover_url',
                'label' => Yii::t('app', 'Preview'),
                'format' => 'raw',
                'value' => function($data){
                    return '<img src="'.Aliyun::absolutePath(!empty($data['cover_url']) ? 
                                $data['cover_url'] : 'static/imgs/notfound.png').'" style="width: 70px"/>';
                },
                'headerOptions' => [
                    'style' => 'width: 80px',
                ],
            ],
            [
                'attribute' => 'media_name',
                'label' => Yii::t('app', '{Resources}{Name}',[
                    'Resources' => Yii::t('app', 'Resources'),
                    'Name' => Yii::t('app', 'Name')
                ]),
            ],
            [
                'attribute' => 'type_name',
                'label' => Yii::t('app', 'Type'),
                'headerOptions' => [
                    'style' => 'width: 50px',
                ],
            ],
            [
                'attribute' => 'duration',
                'label' => Yii::t('app', 'Duration'),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
                'value' => function($data) {
                    return $data['duration'] > 0 ? DateUtil::intToTime($data['duration'], ':', true) : null;
                },
            ],
            [
                'attribute' => 'size',
                'label' => Yii::t('app', 'Size'),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
                'value' => function($data) {
                    return Yii::$app->formatter->asShortSize($data['size']);
                },
            ],
            [
                'attribute' => 'price',
                'label' => Yii::t('app', '{Resources}{Price}',[
                    'Resources' => Yii::t('app', 'Resources'),
                    'Price' => Yii::t('app', 'Price')
                ]),
                'headerOptions' => [
                    'style' => 'width: 90px',
                ],
                'value' => function($data) {
                    return '￥'. $data['price'];
                }
            ],
            [
//                'attribute' => 'num',
                'label' => Yii::t('app', '{Route}{Copy}', [
                    'Route' => Yii::t('app', 'Route'),
                    'Copy' => Yii::t('app', 'Copy'),
                ]),
                'headerOptions' => [
                    'style' => 'width: 180px',
                ],
                'value' => function ($data) {
                    return '超清      高清      标清      流畅';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', '{Problem}{Feedback}', [
                    'Problem' => Yii::t('app', 'Problem'),
                    'Feedback' => Yii::t('app', 'Feedback'),
                ]),
                'template' => '{view}',
                'headerOptions' => ['style' => 'width: 80px'],
                'buttons' => [
                    'view' => function ($url, $data, $key) {
                        $options = [
                           'class' => '',
                           'style' => 'color: #39f',
                           'title' => Yii::t('app', 'View'),
                           'aria-label' => Yii::t('app', 'View'),
                           'data-pjax' => '0',
                           'target' => '_blank'
                       ];
                       $buttonHtml = [
                           'name' => '查看资源',
                           'url' => ['/media_library/media/view', 'id' => $data['media_id']],
                           'options' => $options,
                           'symbol' => '&nbsp;',
                           'conditions' => true,
                           'adminOptions' => true,
                       ];
                       return Html::a($buttonHtml['name'],$buttonHtml['url'],$buttonHtml['options']).' ';
                   },
                ]
            ],
        ],
    ]); ?>
</div>