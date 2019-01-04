<?php

use backend\modules\media_admin\assets\ModuleAsset;
use common\models\media\MediaApprove;
use common\models\media\searchs\MediaRecycleSearh;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel MediaRecycleSearh */
/* @var $dataProvider ActiveDataProvider */

ModuleAsset::register($this);

$this->title = Yii::t('app', 'Recycle Bin');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="media-recycle-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>

    <div class="panel pull-left">
    
        <div class="title">
            <div class="btngroup pull-right">
                <?= Html::a(Yii::t('app', 'Recovery'), ['update', 'result' => MediaApprove::RESULT_PASS_YES], [
                    'id' => 'btn-recovery', 'class' => 'btn btn-primary btn-flat']); ?>
                <?= ' ' . Html::a(Yii::t('app', 'Delete'), ['update', 'result' => MediaApprove::RESULT_PASS_NO], [
                    'id' => 'btn-delete', 'class' => 'btn btn-danger btn-flat']); ?>
            </div>
            
        </div>

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",  
            'columns' => [
                [
                    'header' => Html::checkbox('selectall'),
                    'format' => 'raw',
                    'value' => function($model){
                        return Html::checkbox('stuCheckBox', null, ['value' => $model->id]);
                    },
                    'headerOptions' => [
                        'style' => [
                            'width' => '20px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],
                [
                    'attribute' => 'media_id',
                    'label' => Yii::t('app', '{Media}{Number}', [
                        'Media' => Yii::t('app', 'Media'), 'Number' => Yii::t('app', 'Number')
                    ]),
                    'headerOptions' => [
                        'style' => [
                            'width' => '66px',
                            'padding' => '8px 4px'
                        ]
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ]
                ],

                'result',
                'status',
                'handled_by',
                //'handled_at',
                //'created_by',
                //'created_at',
                //'updated_at',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    
    </div>
    
</div>
