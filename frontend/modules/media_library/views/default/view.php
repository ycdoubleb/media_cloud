<?php

use common\utils\DateUtil;
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
            <span class="media-tags"><?= implode('，', $tagsDataProvider) ?></span>
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
            <div class="resource-list">
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
                                ]),
                                'value' => function($model){
                                    return !empty($model->type_id) ? $model->mediaType->name : null;
                                },
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
                                'label' => Yii::t('app', 'Duration'),
                                'value' => function($model){
                                    return $model->duration > 0 ? DateUtil::intToTime($model->duration, ':', true) : null;
                                },
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
                    <table id="w0" class="table detail-view mc-table">
                    <tbody>
                        <?php foreach ($attrDataProvider as $data): ?>
                        <tr>
                            <th class="detail-th"><?= $data['attr_name'] ?></th>
                            <td class="detail-td"><?= $data['attr_value'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <th class="detail-th">
                                <?= Yii::t('app', '{Media}{Tag}', [
                                    'Media' => Yii::t('app', 'Media'), 'Tag' => Yii::t('app', 'Tag')
                                ]) ?>
                            </th>
                            <td class="detail-td"><?= implode('，', $tagsDataProvider) ?></td>
                        </tr>
                    </tbody>
                    </table>
                </div>
            </div>
            <div class="resource-show">
                <?php
                    $mediaType = $model->mediaType->sign;
                    $mediaUrl = $model->url;
                    switch ($mediaType){
                        case 'video' : 
                            echo '<video src="'.$mediaUrl.'" controls="controls"  width="100%"></video>';
                            break;
                        case 'audio' : 
                            echo '<audio src="'.$mediaUrl.'" style="width:100%"></audio>';
                            break;
                        case 'image' : 
                            echo Html::img($mediaUrl, ['with' => '100%']);
                            break;
                        case 'document' : 
                            echo '<iframe src="http://eezxyl.gzedu.com/?furl='.$mediaUrl.'" width="100%" height="700" style="border: none"></iframe>';
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