<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use common\models\media\Acl;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Acl */

YiiAsset::register($this);
OperationModuleAsset::register($this);

$this->title = Yii::t('app', '{Visits}{Detail}', [
    'Visits' => Yii::t('app', 'Visits'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Visit}{List}', [
    'Visit' => Yii::t('app', 'List'), 'Path' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="acl-view">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">
                <?= Yii::t('app', 'Basic Info') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a href="#action" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', 'Operation Notes') ?>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        
        <!--基本信息-->
        <div role="tabpanel" class="tab-pane fade active in" id="basics" aria-labelledby="basics-tab">
    
            <?= DetailView::widget([
                'model' => $model,
                'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                'attributes' => [
                    [
                        'attribute' => 'id',
                        'label' => Yii::t('app', '{Visits}{ID}', [
                            'Visits' => Yii::t('app', 'Visits'), 'ID' => Yii::t('app', 'ID')
                        ]),
                    ],
                    [
                        'attribute' => 'sn',
                        'label' => Yii::t('app', 'Visits Sn'),
                    ],
                    [
                        'attribute' => 'name',
                        'label' => Yii::t('app', '{Visits}{Name}', [
                            'Visits' => Yii::t('app', 'Visits'), 'Name' => Yii::t('app', 'Name')
                        ]),
                    ],
                    [
                        'label' => Yii::t('app', 'Status'),
                        'value' => Acl::$statusMap[$model->status],
                    ],
                    [
                        'attribute' => 'visit_count',
                        'label' => Yii::t('app', 'Visits Count'),
                    ],
                    [
                        'attribute' => 'media_id',
                        'label' => Yii::t('app', '{Medias}{Number}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
                        ]),
                    ],
                    [
                        'attribute' => 'order_sn',
                        'label' => Yii::t('app', 'Orders Sn'),
                    ],
                    [
                        'label' => Yii::t('app', '{Orders}{Name}', [
                            'Orders' => Yii::t('app', 'Orders'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'value' => !empty($model->order_id) ? $model->order->order_name : null,
                    ],
                    [
                        'label' => Yii::t('app', 'Purchaser'),
                        'value' => !empty($model->user_id) ? $model->user->nickname : null,
                    ],
                    [
                        'label' => Yii::t('app', '{Create}{Time}', [
                            'Create' => Yii::t('app', 'Create'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => $model->created_at > 0 ? date('Y-m-d H:i', $model->created_at) : null,
                    ]
                ],
            ]) ?>
            
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