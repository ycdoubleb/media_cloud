<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\models\media\MediaType;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Media */

YiiAsset::register($this);
MediaModuleAsset::register($this);

/* 判断缩略图是否存在 */
if($model->cover_url != null){
    $cover_url = $model->cover_url;
}else if(isset($iconMap[$model->ext])){
    $cover_url = $iconMap[$model->ext];
}else{
    $cover_url = '';
}

$this->title = Yii::t('app', "{Media}{Detail}：{$model->name}", [
    'Media' => Yii::t('app', 'Media'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Media'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="media-view">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">基本信息</a>
        </li>
        <li role="presentation" class="">
            <a href="#tags" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">标签管理</a>
        </li>
        <li role="presentation" class="">
            <a href="#preview" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">媒体预览</a>
        </li>
        <li role="presentation" class="">
            <a href="#action" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">操作记录</a>
        </li>
    </ul>

    <div class="tab-content">
        
       
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <p>
                <?= Html::a(Yii::t('app', 'Edit'), ['edit-basic', 'id' => $model->id], [
                    'id' => 'btn-editBasic', 'class' => 'btn btn-primary']) ?>
            </p>
            
            <?= DetailView::widget([
                'model' => $model,
                'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('app', '{Media}{Number}', [
                            'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
                        ]),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('app', '{Media}{Name}', [
                            'Media' => Yii::t('app', 'Media'), 'Name' => Yii::t('app', 'Name')
                        ]),
                    ],
                    [
                        'label' => Yii::t('app', '{Media}{Type}', [
                            'Media' => Yii::t('app', 'Media'), 'Type' => Yii::t('app', 'Type')
                        ]),
                        'value' => !empty($model->type_id) ? $model->mediaType->name : null
                    ],
                    [
                        'label' => Yii::t('app', 'Cover Img'),
                        'format' => 'raw',
                        'value' => Html::img($cover_url, ['width' => 112, 'height' => 72])
                    ],
                    [
                        'label' => Yii::t('app', '{Media}{Price}', [
                            'Media' => Yii::t('app', 'Media'), 'Price' => Yii::t('app', 'Price')
                        ]),
                        'value' => Yii::$app->formatter->asCurrency($model->price)
                    ],
                    [
                        'label' => Yii::t('app', '{Storage}{Dir}', [
                            'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
                        ]),
                        'value' => !empty($model->dir_id) ? $model->dir->getFullPath() : null
                    ],
                    [
                        'attribute' => 'duration',
                        'value' => $model->duration > 0 ? DateUtil::intToTime($model->duration, ':', true) : null,  
                    ],
                    [
                        'attribute' => 'size',
                        'value' => Yii::$app->formatter->asShortSize($model->size),  
                    ],
                    [
                        'label' => Yii::t('app', 'Operator'),
                        'value' => !empty($model->owner_id) ? $model->owner->nickname : null,
                    ],
                    [
                        'label' => Yii::t('app', 'Created By'),
                        'value' => !empty($model->created_by) ? $model->createdBy->nickname : null,
                    ],
                    'created_at:datetime',
                    'updated_at:datetime',
                ],
            ]) ?>
            
        </div>
        
        <!--标签管理-->
        <div role="tabpanel" class="tab-pane fade" id="tags" aria-labelledby="config-tab">
            
            <p>
                <?= Html::a(Yii::t('app', 'Edit'), ['edit-attribute', 'id' => $model->id], [
                    'id' => 'btn-editAttribute', 'class' => 'btn btn-primary']) ?>
            </p>
            
            <table id="w1" class="table table-striped table-bordered detail-view">
                
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
        
       <!--视频发布-->
        <div role="tabpanel" class="tab-pane fade" id="preview" aria-labelledby="preview-tab">
            
            <p>
                <?= Html::a(Yii::t('app', '{Anew}{Upload}', [
                    'Anew' => Yii::t('app', 'Anew'), 'Upload' => Yii::t('app', 'Upload')
                ]), ['anew-upload', 'id' => $model->id], ['id' => 'btn-anewUpload', 'class' => 'btn btn-primary']) ?>
                <?php 
                    if($model->mediaType->sign == MediaType::SIGN_VIDEO){
                        echo Html::a(Yii::t('app', '{Anew}{Transcoding}', [
                            'Anew' => Yii::t('app', 'Anew'), 'Transcoding' => Yii::t('app', 'Transcoding')
                        ]), ['anew-transcoding', 'id' => $model->id], ['id' => 'btn-anewTranscoding', 'class' => 'btn btn-primary']);
                    }
                ?>
            </p>
            
            <?php if($model->mediaType->sign == MediaType::SIGN_VIDEO){
                echo GridView::widget([
                    'dataProvider' => $videoDataProvider,
                    'layout' => "{items}\n{summary}\n{pager}",  
                    'columns' => [
                        [
                            'label' => Yii::t('app', 'Name'),
                            'value' => function($model){
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
                            'value' => function($model){
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
                            'value' => function($model){
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
                            'value' => function($model){
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
                                'view' => function($url, $model){
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
            
            /* 根据媒体类型加载不同的预览内容 */
            if(!empty($model->type_id)){
                switch ($model->mediaType->sign){
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
            
        </div>
        
        <!--操作记录-->
        <div role="tabpanel" class="tab-pane fade" id="action" aria-labelledby="action-tab">
          
            <?= GridView::widget([
                'dataProvider' => $actionDataProvider,
                'layout' => "{items}\n{summary}\n{pager}",  
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => [
                                'width' => '30px',
                            ],
                        ],
                    ],
                    [
                        'label' => Yii::t('app', '{Operate}{Type}', [
                            'Operate' => Yii::t('app', 'Operate'), 'Type' => Yii::t('app', 'Type')
                        ]),
                        'value' => function($model){
                            return $model->title;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '120px',
                            ]
                        ],
                    ],
                    [
                        'label' => Yii::t('app', 'Created By'),
                        'value' => function($model){
                            return !empty($model->created_by) ? $model->createdBy->nickname : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '120px',
                            ]
                        ],
                    ],
                    [
                        'label' => Yii::t('app', 'Content'),
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->content;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '750px',
                            ]
                        ],
                    ],
                    [
                        'label' => Yii::t('app', '{Operate}{Time}', [
                            'Operate' => Yii::t('app', 'Operate'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => function($model){
                            return date('Y-m-d H:i', $model->created_at);
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '120px',
                            ]
                        ],
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => '操作',
                        'buttons' => [
                            'view' => function($url, $model){
                                return Html::a(Yii::t('app', 'View'), ['view-action', 'id' => $model->id], [
                                    'id' => 'btn-viewAction', 'class' => 'btn btn-default', 'onclick' => 'showModal($(this)); return false;'
                                ]);
                            },
                        ],
                        'headerOptions' => [
                            'style' => [
                                'width' => '80px',
                            ],
                        ],

                        'template' => '{view}',
                    ],
                ],
            ]); ?>
            
        </div>
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出媒体编辑页面面板
    $('#btn-editBasic, #btn-editAttribute, #btn-anewUpload, #btn-anewTranscoding').click(function(e){
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
        
JS;
    $this->registerJs($js, View::POS_READY);
?>