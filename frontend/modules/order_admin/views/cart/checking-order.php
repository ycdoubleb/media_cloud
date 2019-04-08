<?php

use common\components\aliyuncs\Aliyun;
use common\models\order\searchs\CartSearch;
use common\utils\DateUtil;
use common\utils\I18NUitl;
use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel CartSearch */
/* @var $dataProvider ActiveDataProvider */
/* 下单购买页 */

$this->title = I18NUitl::t('app', '{Checking}{Orders}');

MainAssets::register($this);
ModuleAssets::register($this);

?>

<div class="order_admin mediacloud">
    <!--头部信息-->
    <div class="header checking-header">
        <div class="container">
            <div class="media-top">
                <div class="pull-left">
                    <div class="cloud-name">
                        <span class="cloud-title">素材在线</span>
                        <span class="cloud-website">www.resonline.com</span>
                    </div>
                    <div class="cloud-cart">购物车</div>
                </div>
                <div class="pull-right bar-step">
                    <ul id="progressbar">
                        <li class="old-active">1、我的购物车</li>
                        <li class="active">2、填写核对订单信息</li>
                        <li>3、成功提交订单</li>
                        <li></li>
                    </ul>
                </div>
            </div>
            <div class="information">
                <span>填写素材使用信息</span>
                <div class="use-purpose mc-form">
                    <?php $form = ActiveForm::begin([
                        'action' => ['checking-order'],
                        'method' => 'post',
                        'options' => [
                            'id' => 'order-admin-form',
                            'class' => 'form-horizontal',
                        ],
                        'fieldConfig' => [
                            'template' => "{label}\n<div class=\"col-lg-11 col-md-11\" style=\"padding-left: 0;\">{input}</div>\n",
                            'labelOptions' => [
                                'class' => 'col-lg-1 col-md-1 control-label form-label',
                                'style' => 'padding-left: 0;'
                            ], 
                        ],
                    ]);?>
                    <?= $form->field($model, 'order_name')->textInput([
                            'placeholder' => '填写素材用途或者课程名称', 'maxlength' => true,
                        ])->label('使用目的：');?>

                    <?= $form->field($model, 'user_note')->textInput([
                            'placeholder' => '填写留言', 'maxlength' => true,
                        ])->label('留言：');?>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

    <!--订单信息-->
    <div class="container content">
        <div class="checking-order common">
            <div class="mc-tabs">
                <ul class="list-unstyled">
                    <li class="active">
                        <?= Html::a('素材列表', 'javascript:;', ['title' => '素材列表']);?>
                    </li>
                </ul>
            </div>
            <!--订单列表-->
            <div class="mc-panel set-bottom">
                <div class="meida-table">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
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
                                'attribute' => 'media_id',
                                'label' => Yii::t('app', 'Medias Sn'),
                                'headerOptions' => [
                                    'style' => 'width: 80px',
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
                                'label' => I18NUitl::t('app', '{Medias}{Name}'),
                                'format' => 'raw',
                                'value' => function($data){
                                    return '<span class="multi-line-clamp">'.$data['media_name'].'</span>';
                                }
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
                                    'style' => 'width: 80px',
                                ],
                                'value' => function($data) {
                                    return $data['duration'] > 0 ? DateUtil::intToTime($data['duration'], ':', true) : '';
                                },
                            ],
                            [
                                'attribute' => 'size',
                                'label' => Yii::t('app', 'Size'),
                                'headerOptions' => [
                                    'style' => 'width: 80px',
                                ],
                                'value' => function($data) {
                                    return Yii::$app->formatter->asShortSize($data['size']);
                                },
                            ],
                            [
                                'attribute' => 'price',
                                'label' => I18NUitl::t('app', '{Medias}{Price}'),
                                'headerOptions' => [
                                    'style' => 'width: 80px',
                                ],
                                'value' => function($data) {
                                    return '￥'. $data['price'];
                                }
                            ],
                            [
                                'attribute' => 'num',
                                'label' => I18NUitl::t('app', '{Medias}{Num}'),
                                'headerOptions' => [
                                    'style' => 'width: 100px',
                                ],
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
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
            </div>
        </div>

        <div class="checking-bottom">
            <div class="display-info">
                <div class="pull-right">
                    <div class="meida-num">
                        素材总数：
                        <div class="mp-number"><?= $sel_num;?> 个</div>
                    </div>
                    <div class="payable-price">
                        应付金额：
                        <div class="mp-number">￥<?= $total_price;?></div>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <?= Html::a('提交订单', 'javascript:;', [
                    'id' => 'place_order',
                    'class' => 'btn btn-highlight btn-flat-lg', 'title' => '提交订单']);
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    //提交表单 
    $("#place_order").click(function(){
        $('#order-admin-form').submit();
    })
JS;
    $this->registerJs($js,  View::POS_READY);
?>













