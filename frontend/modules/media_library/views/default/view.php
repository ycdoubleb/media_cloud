<?php

use frontend\modules\media_library\assets\ModuleAssets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

ModuleAssets::register($this);

$this->title = "资源详情";
?>
<!--头部展示媒体信息-->
<div class="header">
    <div class="container">
        <div class="media-title">     
            <span class="media-name"><?= $model->name;?></span>
            <span class="media-tags">数学，思想，角度，教学，设计，老师，未来</span>
        </div>
        <div class="operation">
            <div class="btngroup pull-left">
                <?php
                if($hasFavorite){
                    echo Html::a('<i class="glyphicon glyphicon-heart"></i>', ['del-favorite', 'id' => $model->id], [
                        'class' => 'btn-ellipse fav-red', 'title' => '取消收藏'
                    ]) . '&nbsp;';
                } else {
                    echo Html::a('<i class="glyphicon glyphicon-heart"></i>', ['add-favorite', 'id' => $model->id], [
                        'class' => 'btn-ellipse', 'title' => '加入收藏'
                    ]) . '&nbsp;';
                }
                echo Html::a('<i class="glyphicon glyphicon-question-sign"></i>', ['feedback', 'id' => $model->id], [
                    'class' => 'btn-ellipse', 'title' => '反馈问题',
                    'onclick' => 'showModal($(this).attr("href"));return false;'
                ]);
                ?>
            </div>
            <div class="btngroup pull-right">
                <?php
                echo Html::a('加入购物车', ['add-cart', 'id' => $model->id], [
                    'class' => 'btn btn-highlight btn-flat', 'title' => '加入购物车'
                ]) . '&nbsp;';
                echo Html::a('立即购买', ['checking-order', 'id' => $model->id], [
                    'class' => 'btn btn-highlight btn-flat', 'title' => '立即购买'
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<!--中部展示媒体详细信息-->
<div class="container content">
    <div class="default-view common">
        <div class="mc-tabs">
            <ul class="list-unstyled">
                <li id="details">
                    <?= Html::a('详情', ['view', 'pages' => 'details'], [
                        'id' => 'list', 'title' => '详情'
                    ]);?>
                </li>
                <li id="comment">
                    <?= Html::a('评论', ['view', 'pages' => 'comment'], [
                        'id' => 'list', 'title' => '评论'
                    ]);?>
                </li>
            </ul>
        </div>
        <div class="mc-panel set-bottom">
            <div class="panel-left">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table detail-view mc-table'],
                    'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                    'attributes' => [
                        [
                            'attribute' => 'id',
                            'label' => Yii::t('app', 'Resources Sn')
                        ],
                        [
                            'attribute' => 'type_id',
                            'label' => Yii::t('app', '{Resources}{Type}',[
                                'Resources' => Yii::t('app', 'Resources'),
                                'Type' => Yii::t('app', 'Type'),
                            ])
                        ],
                        [
                            'attribute' => 'name',
                            'label' => Yii::t('app', '{Resources}{Name}',[
                                'Resources' => Yii::t('app', 'Resources'),
                                'Name' => Yii::t('app', 'Name'),
                            ])
                        ],
                        [
                            'attribute' => 'price',
                        ],
                        [
                            'attribute' => 'duration',
                            'label' => Yii::t('app', 'Duration')
                        ],
                        [
                            'attribute' => 'size',
                            'value' => function($model) {
                                return Yii::$app->formatter->asShortSize($model->size);
                            }
                        ],
                    ],
                ]);?>
            </div>
            <div class="panel-right">
                <?= DetailView::widget([
                    'model' => $model,
                    'options' => ['class' => 'table detail-view mc-table'],
                    'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                    'attributes' => [
                        [
                            'attribute' => 'id',
                            'label' => Yii::t('app', 'Definition')
                        ],
                        [
                            'attribute' => 'type_id',
                            'label' => Yii::t('app', '{Education}{Copyright}',[
                                'Education' => Yii::t('app', 'Education'),
                                'Copyright' => Yii::t('app', 'Copyright'),
                            ])
                        ],
                        [
                            'attribute' => 'id',
                            'label' => Yii::t('app', 'Tag')
                        ],
                        [
                            'attribute' => 'duration',
                            'label' => Yii::t('app', '{Major}/{Engineering}',[
                                'Major' => Yii::t('app', 'Major'),
                                'Engineering' => Yii::t('app', 'Engineering'),
                            ])
                        ],
                        [
                            'attribute' => 'duration',
                            'label' => Yii::t('app', 'Copyright')
                        ],
                        [
                            'attribute' => 'duration',
                            'label' => Yii::t('app', 'Learning section')
                        ],
                    ]
                ]);?>
            </div>
            <div class="resource-show">
                <?php
                    $mediaType = $model->mediaType->name;
                    $mediaUrl = common\components\aliyuncs\Aliyun::absolutePath($model->uploadfile->path);
                    switch ($mediaType){
                        case '视频' : 
                            echo '<video src="'.$mediaUrl.'"></video>';
                            break;
                        case '音频' : 
                            echo '<audio src="'.$mediaUrl.'"></audio>';
                            break;
                        case '图片' : 
                            echo Html::img($mediaUrl);
                            break;
                        case '文档' : 
                            echo '文档';
                            break;
                        default : 
                            echo '默认类型';
                            break;
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
$pages = ArrayHelper::getValue($filters, 'pages', 'details');   //排序
$js = <<<JS
    //选中效果
    $(".mc-tabs ul li[id=$pages]").addClass('active'); 
JS;
$this->registerJs($js, View::POS_READY);
?>