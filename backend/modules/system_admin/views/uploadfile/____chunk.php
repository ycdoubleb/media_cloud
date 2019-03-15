<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="file-index">
    
    <div class="pull-right">
        <?php
        echo Html::a('删除分片', 'javascript:;', ['id' => 'delete',
            'data-url' => Url::to(['uploadfile/del-chunk']),
            'class' => 'btn btn-highlight btn-flat-lg', 'title' => '删除分片',
        ])?>
    </div>
    
    <div class="content-panel">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-bordered table-striped mc-table'],
            'layout' => "{items}\n{summary}\n{pager}",
            'rowOptions'=>function($searchModel){
                return ['id' => "tr-".$searchModel['chunk_id'], 'data-value' => $searchModel['chunk_id']];
            },
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
                    'headerOptions' => [
                        'style' => 'width: 30px',
                    ],
                ],
                [
                    'attribute' => 'chunk_id',
                    'label' => Yii::t('app', 'Chunk Id'),
                    'headerOptions' => [
                        'style' => 'width: 220px'
                    ],
                ],
                [
                    'attribute' => 'file_md5',
                    'label' => Yii::t('app', 'File MD5'),
                    'headerOptions' => [
                        'style' => 'width: 220px'
                    ],
                ],
                [
                    'attribute' => 'chunk_path',
                    'label' => Yii::t('app', 'Path'),
                ],
                [
                    'attribute' => 'chunk_index',
                    'label' => Yii::t('app', 'Chunk Index'),
                    'headerOptions' => [
                        'style' => 'width: 80px'
                    ],
                ],
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('app', 'Created At'),
                    'headerOptions' => [
                        'style' => 'width: 95px'
                    ],
                    'value' => function ($data) {
                        return date('Y-m-d H:i', $data['created_at']); 
                    }
                ],
            ],
        ]); ?>
    </div>
</div>