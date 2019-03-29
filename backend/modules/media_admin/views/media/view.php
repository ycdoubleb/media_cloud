<?php

use backend\modules\media_admin\assets\MediaModuleAsset;
use common\models\media\Media;
use common\models\media\MediaType;
use common\modules\rbac\components\Helper;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
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

$this->title = Yii::t('app', "{Medias}{Detail}", [
    'Medias' => Yii::t('app', 'Medias'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', "{Medias}{List}", [
    'Medias' => Yii::t('app', 'Medias'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="media-view">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">
                <?= Yii::t('app', 'Basic Info') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a id="tags-admin" href="#tags" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', '{Tags}{Admin}', [
                    'Tags' => Yii::t('app', 'Tags'), 'Admin' => Yii::t('app', 'Admin')
                ]) ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a id="media-preview" href="#preview" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', '{Medias}{Preview}', [
                    'Medias' => Yii::t('app', 'Medias'), 'Preview' => Yii::t('app', 'Preview')
                ]) ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a id="action-log" href="#action" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', 'Operation Notes') ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        
       
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
            
            <p>
                <?php 
                    if( Helper::checkRoute(Url::to(['edit-basic']))){
                        echo Html::a(Yii::t('app', 'Edit'), ['edit-basic', 'id' => $model->id], [
                            'id' => 'btn-editBasic', 'class' => 'btn btn-primary']);
                    }
                ?>
            </p>
            
            <?= DetailView::widget([
                'model' => $model,
                'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('app', 'Number'),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('app', '{Medias}{Name}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
                        ]),
                    ],
                    [
                        'label' => Yii::t('app', '{Medias}{Type}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Type' => Yii::t('app', 'Type')
                        ]),
                        'value' => !empty($model->type_id) ? $model->mediaType->name : null
                    ],
                    [
                        'label' => Yii::t('app', 'Cover Img'),
                        'format' => 'raw',
                        'value' => Html::img($cover_url, ['width' => 112, 'height' => 72])
                    ],
                    [
                        'label' => Yii::t('app', 'Price'),
                        'value' => Yii::$app->formatter->asCurrency($model->price)
                    ],
                    [
                        'label' => Yii::t('app', 'Storage Dir'),
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
                    [
                        'label' => Yii::t('app', '{Medias}{Content}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Content' => Yii::t('app', 'Content')
                        ]),
                        'value' => !empty($model->detail) ? $model->detail->content : null,
                    ],
                ],
            ]) ?>
            
        </div>
        
        <!--标签管理-->
        <div role="tabpanel" class="tab-pane fade" id="tags" aria-labelledby="config-tab">
            
            <!--加载中-->
            <div class="loading-box">
                <span class="loading"></span>
            </div>
            
        </div>
        
       <!--素材预览-->
        <div role="tabpanel" class="tab-pane fade" id="preview" aria-labelledby="preview-tab">
            
            <!--加载中-->
            <div class="loading-box">
                <span class="loading"></span>
            </div>
            
        </div>
        
        <!--操作记录-->
        <div role="tabpanel" class="tab-pane fade" id="action" aria-labelledby="action-tab">
          
            <!--加载中-->
            <div class="loading-box">
                <span class="loading"></span>
            </div>
            
        </div>
        
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 显示标签管理
    $('#tags-admin').click(function(){
        $('#tags').load("/media_admin/media/tagsadmin?id={$model->id}");
    });    
        
    // 显示素材预览
    $('#media-preview').click(function(){
        $('#preview').load("/media_admin/media/preview?id={$model->id}");
    });   
        
    // 显示素材操作
    $('#action-log').click(function(){
        $('#action').load("/media_admin/media/operatelog-list?id={$model->id}");
    });    
        
    // 弹出素材编辑页面面板
    $('#btn-editBasic').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
        
JS;
    $this->registerJs($js, View::POS_READY);
?>