<?php

use common\utils\DateUtil;
use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\media_library\assets\ModuleAssets;
use kartik\growl\GrowlAsset;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

MainAssets::register($this);
ModuleAssets::register($this);
GrowlAsset::register($this);

$this->title = Yii::t('app', '{Media}{Detail}', [
    'Media' => Yii::t('app', 'Media'),
    'Detail' => Yii::t('app', 'Detail')
]);
?>

<div class="media_library mediacloud">
    <!--头部展示媒体信息-->
    <div class="header">
        <div class="container">
            <div class="media-title">     
                <span class="media-name"><?= $model->name;?></span>
                <span class="media-tags multi-line-clamp"><?= $tagsInfo; ?></span>
            </div>
            <div class="operation">
                <div class="btngroup panel-left">
                    <?php
                        $class = $hasFavorite ? 'fav-red' : '';
                        $title = $hasFavorite ? '取消收藏' : '加入收藏';
                        echo Html::a('<i class="glyphicon glyphicon-heart"></i>', 'javascript:;', [
                            'data-url' => Url::to('change-favorite?id='.$model->id),
                            'class' => "change-favorite btn-ellipse $class", 'title' => $title
                        ]) . '&nbsp;';
                        echo Html::a('<i class="glyphicon glyphicon-question-sign"></i>', ['feedback', 'id' => $model->id], [
                            'class' => 'btn-ellipse', 'title' => '反馈问题',
                            'onclick' => 'showModal($(this).attr("href"));return false;'
                        ]);
                    ?>
                </div>
                <div class="btngroup panel-right">
                    <?php
                        echo Html::a('加入购物车', ['add-cart', 'id' => $model->id], [
                            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '加入购物车'
                        ]);
                        echo Html::a('立即购买', ['checking-order', 'id' => $model->id], [
                            'style' => 'margin-right:0px;',
                            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '立即购买'
                        ]);
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!--中部展示媒体详细信息-->
    <div class="container content">
        <div class="media-view common">
            <div class="mc-tabs">
                <ul class="list-unstyled">
                    <li id="details">
                        <?= Html::a('详情', 'javascript:;', [
                            'id' => 'list', 'title' => '详情'
                        ]);?>
                    </li>
                    <li id="comment">
                        <?= Html::a('评论', 'javascript:;', [
                            'id' => 'list', 'title' => '评论'
                        ]);?>
                    </li>
                </ul>
            </div>
            <!--详细信息-->
            <div class="mc-panel set-bottom">
                <div class="resource-list">
                    <table id="w0" class="table detail-view mc-table">
                        <tbody>
                            <?php for($i=0; $i< count($datas)-1; $i += 2): ?>
                                <tr>
                                    <th class="detail-th"><?= $datas[$i]['label'] ?></th><td class="detail-td"><?= $datas[$i]['value'];?></td>
                                    <th class="detail-th"><?= $datas[$i+1]['label'] ?></th><td class="detail-td"><?= $datas[$i+1]['value'];?></td>
                                </tr>
                            <?php endfor;?>
                            
                            <tr>
                                <th class="detail-th">标签</th><td class="detail-td" colspan="3"><?= $tagsInfo;?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!--媒体预览-->
                <div class="resource-show">
                    <?php
                        $mediaType = $model->mediaType->sign;
                        $mediaUrl = $model->url;
                        switch ($mediaType){
                            case 'video' : 
                                echo '<video src="'.$mediaUrl.'" controls="controls" width="100%"></video>';
                                break;
                            case 'audio' : 
                                echo '<audio src="'.$mediaUrl.'" controls="controls" style="width:100%"></audio>';
                                break;
                            case 'image' : 
                                echo Html::img($mediaUrl, ['style' => 'width:100%']);
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
</div>
<?php
$pages = ArrayHelper::getValue($filters, 'pages', 'details');   //排序
$js = <<<JS
    // 选中效果
    $(".mc-tabs ul li[id=$pages]").addClass('active');
        
    // 加入收藏 or 取消收藏
    $(".change-favorite").click(function(){
        var _this = $(this);
        var url=$(this).attr('data-url');
        $.get(url, function(rel){
            if(rel['code'] == '0'){
                if(rel['data']){
                    _this.addClass("fav-red");
                    _this.attr("title", "取消收藏");
                    $.notify({
                        message: '加入收藏成功' 
                    },{
                        type: 'success'
                    });
                }else{
                    _this.removeClass("fav-red");
                    _this.attr("title", "加入收藏");
                    $.notify({
                        message: '取消收藏成功' 
                    },{
                        type: 'success'
                    });
                }
            }else{
                $.notify({
                    message: '操作失败' 
                },{
                    type: 'danger'
                });
            }
        });
    })
JS;
$this->registerJs($js, View::POS_READY);
?>