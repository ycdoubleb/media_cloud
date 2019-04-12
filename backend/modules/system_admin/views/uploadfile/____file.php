<?php

use common\modules\webuploader\models\Uploadfile;
use kartik\widgets\Select2;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="file-index">
    
    <div class="pull-right">
        <?= Html::a(Yii::t('app', '{Delete}{File}', [
            'Delete' => Yii::t('app', 'Delete'), 'File' => Yii::t('app', 'File')
        ]), ['uploadfile/del-file'], ['id' => 'delete',
            'data-url' => Url::to(['uploadfile/del-file']),
            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '删除文件',
        ])?>
    </div>
    
    <div class="content-panel">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
            'layout' => "{items}\n{summary}\n{pager}",
            'rowOptions'=>function($searchModel){
                return ['id' => "tr-".$searchModel['id'], 'data-value' => $searchModel['id']];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'headerOptions' => [
                        'style' => 'width: 30px',
                    ],
                ],
                [
                    'attribute' => 'id',
                    'label' => Yii::t('app', 'ID'),
                    'headerOptions' => [
                        'style' => 'width: 50px'
                    ],
                    'filter' => false,
                ],
                [
                    'attribute' => 'md5',
                    'label' => Yii::t('app', 'File MD5'),
                    'headerOptions' => [
                        'style' => 'width: 220px'
                    ],
                ],
                [
                    'attribute' => 'name',
                    'label' => Yii::t('app', 'Name'),
                ],
                [
                    'attribute' => 'path',
                    'label' => Yii::t('app', 'Path'),
                    'filter' => false,
                ],
                [
                    'attribute' => 'size',
                    'label' => Yii::t('app', 'Size'),
                    'headerOptions' => [
                        'style' => 'width: 100px'
                    ],
                    'filter' => false,
                    'value' => function ($data) {
                        return Yii::$app->formatter->asShortSize($data['size']);
                    }
                ],
                [
                    'attribute' => 'oss_upload_status',
                    'label' => Yii::t('app', 'OSS Upload Status'),
                    'headerOptions' => ['style' => 'width: 115px'],
                    'filter' => Select2::widget([
                        //'value' => null,
                        'model' => $searchModel,
                        'attribute' => 'oss_upload_status',
                        'data' => Uploadfile::$ossUploadStatus,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]),
                    'value' => function ($data) {
                        return Uploadfile::$ossUploadStatus[$data['oss_upload_status']];
                    }
                ],
                [
                    'attribute' => 'created_by',
                    'label' => Yii::t('app', 'Created By'),
                    'headerOptions' => ['style' => 'width: 115px'],
                    'filter' => Select2::widget([
                        //'value' => null,
                        'model' => $searchModel,
                        'attribute' => 'created_by',
                        'data' => $createdBy,
                        'hideSearch' => true,
                        'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')],
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                    ]),
                    'value' => function ($data) {
                        return $data['created_by'];
                    }
                ],
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('app', 'Created At'),
                    'headerOptions' => [
                        'style' => 'width: 95px'
                    ],
                    'filter' => false,
                    'value' => function ($data) {
                        return date('Y-m-d H:i', $data['created_at']); 
                    }
                ],
            ],
        ]); ?>
    </div>
</div>