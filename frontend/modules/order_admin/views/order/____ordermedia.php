<?php

use common\components\aliyuncs\Aliyun;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\Html;

/**
 * simple-view 订单核查页的子页面
 * 订单商品页
 */

?>
<div class="meida-table">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
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
                'attribute' => 'goods_id',
                'label' => Yii::t('app', 'Media Sn'),
                'headerOptions' => [
                    'style' => 'width: 120px',
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
                'label' => Yii::t('app', '{Media}{Name}',[
                    'Media' => Yii::t('app', 'Media'),
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
                    'style' => 'width: 100px',
                ],
                'value' => function($data) {
                    return Yii::$app->formatter->asShortSize($data['size']);
                },
            ],
            [
                'attribute' => 'price',
                'label' => Yii::t('app', '{Media}{Price}',[
                    'Media' => Yii::t('app', 'Media'),
                    'Price' => Yii::t('app', 'Price')
                ]),
                'headerOptions' => [
                    'style' => 'width: 100px',
                ],
                'value' => function($data) {
                    return '￥'. $data['price'];
                }
            ],
            [
//                'attribute' => 'num',
                'label' => Yii::t('app', 'Num'),
                'headerOptions' => [
                    'style' => 'width: 50px',
                ],
                'value' => function ($data) {
                    return 1;
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app', 'Operation'),
                'template' => '{view}',
                'headerOptions' => ['style' => 'width: 100px'],
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
                           'name' => '查看详情',
                           'url' => ['/media_library/media/view', 'id' => $data['goods_id']],
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