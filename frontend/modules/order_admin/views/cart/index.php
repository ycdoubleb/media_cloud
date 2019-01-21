<?php

use common\components\aliyuncs\Aliyun;
use common\models\order\searchs\CartSearch;
use common\utils\DateUtil;
use frontend\modules\order_admin\assets\ModuleAssets;
use kartik\growl\GrowlAsset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel CartSearch */
/* @var $dataProvider ActiveDataProvider */

ModuleAssets::register($this);
GrowlAsset::register($this);

$this->title = Yii::t('app', 'Cart');

?>
<div class="cart-index main">
    <div class="mc-title">
        <span><?= Yii::t('app', '{My}{Cart}', ['My' => Yii::t('app', 'My'), 'Cart' => Yii::t('app', 'Cart')])?></span>
    </div>
    <?= $this->render('_search', [
        'searchModel' => $searchModel,
        'sel_num' => $sel_num,
        'total_price' => $total_price,
    ]); ?>
    <div class="mc-panel clear-margin">
        <div class="favorites-table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
                'layout' => "{items}\n{pager}\n{summary}",
                'rowOptions'=>function($searchModel){
                    return ['id' => "tr-".$searchModel['media_id'], 'data-value' => $searchModel['media_id']];
                },
                'columns' => [
                    [
                        // 'class' => 'yii\grid\CheckboxColumn',
                        'header' => Html::checkbox('selection_all', 
                            ($totalCount != 0 ? $totalCount == $sel_num : false), ['id' => 'change-all']),
                        'headerOptions' => [
                            'style' => 'width: 30px',
                        ],
                        'format' => 'raw',
                        'value' => function($data){
                            return Html::checkbox('selection[]', $data['is_selected'], 
                                    ['value' => $data['media_id'], 'class' => 'change-one']);
                        }
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
                        'attribute' => 'media_id',
                        'label' => Yii::t('app', 'Media Sn'),
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
                        'format' => 'raw',
                        'value' => function($data){
                            return '<span class="multi-line-clamp" style="-webkit-line-clamp:3">'.$data['media_name'].'</span>';
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
                        'attribute' => 'price',
                        'label' => Yii::t('app', '{Media}{Price}',[
                            'Media' => Yii::t('app', 'Media'),
                            'Price' => Yii::t('app', 'Price')
                        ]),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function($data) {
                            return '￥'. $data['price'];
                        }
                    ],
                    [
                        'attribute' => 'num',
                        'label' => Yii::t('app', 'Num'),
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
                        'attribute' => 'created_at',
                        'label' => Yii::t('app', '{Add}{Time}',[
                            'Add' => Yii::t('app', 'Add'),
                            'Time' => Yii::t('app', 'Time')
                        ]),
                        'headerOptions' => [
                            'style' => 'width: 90px',
                        ],
                        'value' => function ($data) {
                           return date('Y-m-d H:i', $data['created_at']); 
                        },
                        'contentOptions' => ['style' => 'font-size: 13px'],
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

<?php
$js = <<<JS
    /**
     * 提交更改所有
     */
    $("#change-all").click(function(){
        var checked = this.checked;
        $.post('/order_admin/cart/change-all', {checked}, function(rel){
            location.reload();  //刷新页面
            if(rel['code'] == '0'){
                $('input[name="selection[]"]').each(function(){
                    $(this).prop("checked", rel['data']);
                });
            }else{
                $.notify({
                    message: '失败' 
                },{
                    type: 'danger'
                });
            }
        });
    });
        
    /**
     * 提交单个更改
     */
    $(".change-one").click(function(){
        var id = this.value;
        $.post('/order_admin/cart/change-one', {id}, function(rel){
            location.reload();  //刷新页面
            if(rel['code'] == '0'){
                $('input[name="selection_all"]').prop("checked", rel['data']);
            }else{
                $.notify({
                    message: '失败' 
                },{
                    type: 'danger'
                });
            }
        });
    });
JS;
    $this->registerJs($js,  View::POS_READY);
?>