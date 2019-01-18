<?php

use common\components\aliyuncs\Aliyun;
use common\models\media\Acl;
use common\models\order\Order;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * view 订单详情页的子页面
 * 媒体列表信息页
 */

?>
<div class="media-list-index">
    <div class="meida-table">
        <?= GridView::widget([
            'dataProvider' => $resourcesData,
            'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
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
                        'style' => 'width: 90px',
                    ],
                ],
                [
                    'attribute' => 'cover_url',
                    'label' => Yii::t('app', 'Preview'),
                    'format' => 'raw',
                    'value' => function($data){
                        return '<img src="'.Aliyun::absolutePath(!empty($data['cover_url']) ? 
                                    $data['cover_url'] : 'static/imgs/notfound.png').'" style="height: 48px"/>';
                    },
                    'headerOptions' => [
                        'style' => 'width: 100px',
                    ],
                ],
                [
                    'attribute' => 'media_name',
                    'label' => Yii::t('app', '{Media}{Name}',[
                        'Media' => Yii::t('app', 'Media'),
                        'Name' => Yii::t('app', 'Name')
                    ]),
                    'format' => 'raw',
                    'value' => function ($data) {
                        return '<span class="multi-line-clamp">' . $data['media_name'] . '</span>';
                    },
                ],
                [
                    'attribute' => 'type_name',
                    'label' => Yii::t('app', 'Type'),
                    'headerOptions' => [
                        'style' => 'width: 50px',
                    ],
                    'value' => function($data) {
                        return $data['type_name'];
                    },
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
                    'label' => Yii::t('app', '{Media}{Price}',[
                        'Media' => Yii::t('app', 'Media'),
                        'Price' => Yii::t('app', 'Price')
                    ]),
                    'headerOptions' => [
                        'style' => 'width: 90px',
                    ],
                    'value' => function($data) {
                        return Yii::$app->formatter->asCurrency($data['price']);
                    },
                ],
                [
                    'label' => Yii::t('app', '{Route}{Copy}', [
                        'Route' => Yii::t('app', 'Route'),
                        'Copy' => Yii::t('app', 'Copy'),
                    ]),
                    'headerOptions' => [
                        'style' => 'width: 135px',
                    ],
                    'format' => 'raw',
                    'value' => function ($data) use($model) {
                        foreach ($data['acl'] as $key => $value){
                            // 访问链接
                            $urls = Url::to(["/media/use/link?sn=$value"], true);
                            switch ($key){
                                case Acl::LEVEL_ORIGINAL:   //原始
                                    $contents = '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a><br>';
                                    break;
                                case Acl::LEVEL_LD:         //流畅
                                    $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>&nbsp';
                                    break;
                                case Acl::LEVEL_SD:         //标清
                                    $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>&nbsp';
                                    break;
                                case Acl::LEVEL_HD:         //高清 
                                    $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>&nbsp';
                                    break;
                                case Acl::LEVEL_FD:         //超清
                                    $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>';
                                    break;
                            }
                        }
                        return ($model->order_status == Order::ORDER_STATUS_TO_BE_CONFIRMED || $model->order_status == Order::ORDER_STATUS_CONFIRMED) 
                            ? $contents : '<span style="color:#ccc">支付成功后方可使用</span>';
                    },
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
</div>