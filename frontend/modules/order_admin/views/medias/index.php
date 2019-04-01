<?php

use common\components\aliyuncs\Aliyun;
use common\models\media\Acl;
use common\models\order\searchs\OrderSearch;
use common\utils\DateUtil;
use common\utils\I18NUitl;
use frontend\modules\order_admin\assets\ModuleAssets;
use kartik\growl\GrowlAsset;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $searchModel OrderSearch */
/* @var $dataProvider ActiveDataProvider */

ModuleAssets::register($this);
GrowlAsset::register($this);

$this->title = Yii::t('app', 'Medias');

?>
<div class="resources-index main">
    <div class="mc-title">
        <span><?= I18NUitl::t('app', '{My}{Medias}')?></span>
    </div>
    <?= $this->render('_search', [
        'searchModel' => $searchModel,
        'filters' => $filters
    ]); ?>
    <div class="mc-panel clear-margin">
        <div class="order-table">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
                'layout' => "{items}\n{pager}\n{summary}",
                'columns' => [
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
                        'label' => I18NUitl::t('app', '{Medias}{Number}'),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                    ],
                    [
                        'attribute' => 'media_name',
                        'label' => I18NUitl::t('app', '{Medias}{Name}'),
                        'format' => 'raw',
                        'value' => function($data){
                            return '<span class="multi-line-clamp" style="-webkit-line-clamp:3">'.$data['media_name'].'</span>';
                        }
                    ],
                    [
                        'attribute' => 'order_sn',
                        'label' => I18NUitl::t('app', '{Orders Sn}/{Name}'),
                        'headerOptions' => [
                            'style' => [
                                'width' => '140px',
                            ],
                        ],
                        'format' => 'raw',
                        'value' => function($data){
                            return $data['order_sn'].'<br/><span class="multi-line-clamp">'.$data['order_name'].'</span>';
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
                        'label' => I18NUitl::t('app', '{Medias}{Price}'),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function($data) {
                            return '￥'. $data['price'];
                        }
                    ],                    
//                    [
//                        'attribute' => 'order_amount',
//                        'label' => Yii::t('app', '{Order}{Amount}', [
//                            'Order' => Yii::t('app', 'Order'), 'Amount' => Yii::t('app', 'Amount')
//                        ]),
//                        'headerOptions' => [
//                            'style' => [
//                                'width' => '90px',
//                            ],
//                        ],
//                    ],
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
                        'format' => 'raw',
                        'label' => Yii::t('app', 'Size'),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function($data) {
                            return Yii::$app->formatter->asShortSize($data['size']);
                        },
                    ],            
                    [
                        'attribute' => 'created_at',
                        'label' => I18NUitl::t('app', '{Buy}{Time}'),
                        'headerOptions' => [
                            'style' => 'width: 80px',
                        ],
                        'value' => function ($data) {
                           return date('Y-m-d H:i', $data['created_at']); 
                        },
                        'contentOptions' => ['style' => 'font-size: 13px'],
                    ],
                    [
                        'label' => I18NUitl::t('app', '{Copy}{Route}'),
                        'headerOptions' => [
                            'style' => [
                                'width' => '90px',
                            ],
                        ],
                        'format' => 'raw',
                        'value' => function ($data) {
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
                                        $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a><br>';
                                        break;
                                    case Acl::LEVEL_HD:         //高清 
                                        $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>&nbsp';
                                        break;
                                    case Acl::LEVEL_FD:         //超清
                                        $contents .= '<a href="javascript:;" data-clipboard-text="'.$urls.'" style="color:#39f">'.Acl::$levelMap[$key].'</a>';
                                        break;
                                }
                            }
                            return $contents;
                        }
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view}',
                        'headerOptions' => ['style' => 'width: 65px'],
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
                                   'name' => '详情',
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
        
    //点击复制视频地址
    var btns = document.querySelectorAll('a');
    var clipboard = new ClipboardJS(btns);
    clipboard.on('success', function(e) {
        $.notify({
            message: '复制成功',
        },{
            type: "success",
        });
    });
    clipboard.on('error', function(e) {
        $.notify({
            message: '复制失败',
        },{
            type: "danger",
        });
    });
JS;
$this->registerJs($js, View::POS_READY);
?>