<?php

use common\models\media\Media;
use common\models\media\MediaType;
use common\modules\rbac\components\Helper;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<p>
    <?php
    if (Helper::checkRoute(Url::to(['anew-upload'])) || Helper::checkRoute(Url::to(['anew-transcoding']))) {
        echo Html::a(Yii::t('app', '{Anew}{Upload}', [
                    'Anew' => Yii::t('app', 'Anew'), 'Upload' => Yii::t('app', 'Upload')
                ]), ['anew-upload', 'id' => $model->id], ['id' => 'btn-anewUpload', 'class' => 'btn btn-primary']);

        if ($model->mediaType->sign == MediaType::SIGN_VIDEO && $model->status == Media::STATUS_PUBLISHED) {
            echo ' ' . Html::a(Yii::t('app', '{Anew}{Transcoding}', [
                        'Anew' => Yii::t('app', 'Anew'), 'Transcoding' => Yii::t('app', 'Transcoding')
                    ]), ['anew-transcoding', 'id' => $model->id], ['id' => 'btn-anewTranscoding', 'class' => 'btn btn-primary']);
        }
        if (!$model->detail->mts_need) {
            echo '<span style="color:#ff0000">（注意：该素材设置了“手动转码”，重新上传文件后如需“转码”，必须手动选择“重新转码”）</span>';
        } else {
            echo '<span class="text-default">（注意：该素材设置了“自动转码”，重新上传文件后会自动转码，请勿多次转码！）</span>';
        }
    }
    ?>

</p>

<!--加载中-->
<div class="loading-box">
    <span class="loading" style="display: none"></span>
    <span class="no-more" style="display: none">转码中...</span>
</div>

<?php
if ($model->mediaType->sign == MediaType::SIGN_VIDEO) {
    echo GridView::widget([
        'dataProvider' => $videoDataProvider,
        'layout' => "{items}\n{summary}\n{pager}",
        'columns' => [
            [
                'label' => Yii::t('app', 'Name'),
                'value' => function($model) {
                    return $model->name;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '185px',
                    ]
                ],
            ],
            [
                'label' => Yii::t('app', 'Bitrate'),
                'value' => function($model) {
                    return $model->bitrate;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '250px',
                    ]
                ],
            ],
            [
                'label' => Yii::t('app', 'Size'),
                'format' => 'raw',
                'value' => function($model) {
                    return Yii::$app->formatter->asShortSize($model->size);
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '330px',
                    ]
                ],
            ],
            [
                'label' => Yii::t('app', 'Format'),
                'value' => function($model) {
                    return !empty($model->media_id) ? $model->media->ext : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '330px',
                    ]
                ],
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'buttons' => [
                    'view' => function($url, $model) {
                        return Html::a(Yii::t('app', 'Preview'), null, [
                                    'class' => 'btn btn-default',
                                    'data-url' => $model->url,
                                    'onclick' => 'videoPreview($(this));'
                        ]);
                    },
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '120px',
                    ],
                ],
                'template' => '{view}',
            ],
        ],
    ]);
}

/* 根据素材类型加载不同的预览内容 */
if (!empty($model->type_id)) {
    switch ($model->mediaType->sign) {
        case MediaType::SIGN_VIDEO :
            echo "<video id=\"myVideo\" src=\"{$model->url}\"  poster=\"{$model->cover_url}\" width=\"100%\" controls=\"controls\"></video>";
            break;
        case MediaType::SIGN_AUDIO :
            echo "<audio src=\"{$model->url}\" style=\"width: 100%\" controls=\"controls\"></audio>";
            break;
        case MediaType::SIGN_IMAGE :
            echo "<img src=\"{$model->url}\" width=\"100%\" />";
            break;
        case MediaType::SIGN_DOCUMENT :
            echo "<iframe src=\"http://eezxyl.gzedu.com/?furl={$model->url}\" width=\"100%\" height=\"700\" style=\"border: none\"></iframe>";
            break;
    }
}
?>

<?php
// 素材类型是转码中
$isTranscoding = $model->mts_status == Media::MTS_STATUS_DOING ? 1 : 0;
$js = <<<JS
   
    var ref = "";
    var isTranscoding = $isTranscoding;
        
    // 弹出素材编辑页面面板
    $('#btn-anewUpload, #btn-anewTranscoding').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
     
    /**
     * 视频预览
     * @param {object} _this
     * @returns {undefined}
     */
    window.videoPreview = function(_this){
        var url = _this.attr('data-url');
        var myVideo = $('#myVideo');
        myVideo.attr('src', url);
        myVideo[0].play();
    } 
    
    // 设置定时查看视频输出的转码情况
    if(isTranscoding){
        ref = setInterval(function(){
            $.get("/media_admin/media/check-transcode?id={$model->id}", function(response){
                if(response.code == "0"){
                    if(response.data.mts_status == 3){
                        $('.loading-box .no-more').text(response.msg);
                        $('.loading-box .loading, .loading-box .no-more').hide();
                        $('#preview').load("/media_admin/media/preview?id={$model->id}");
                        clearInterval(ref);  // 阻止定时
                    }else if(response.data.mts_status == 4){
                        $('.loading-box .no-more').text(response.msg);
                        clearInterval(ref);  // 阻止定时
                    }
                }
            });
        }, 5000);    
        $('.loading-box .loading, .loading-box .no-more').show();
    }    
    
JS;
    $this->registerJs($js, View::POS_READY);
?>