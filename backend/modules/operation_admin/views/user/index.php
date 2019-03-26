<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use common\models\searchs\UserSearch;
use common\models\User;
use common\models\UserProfile;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel UserSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', '{Frontend}{User}', [
    'Frontend' => Yii::t('app', 'Frontend'), 'User' => Yii::t('app', 'User')
]);
$this->params['breadcrumbs'][] = Yii::t('app', '{User}{List}', [
    'User' => Yii::t('app', 'User'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="user-index">

    <?= $this->render('_search', [
        'model' => $searchModel,
    ]) ?>

    <div class="panel pull-left">
        
        <div class="title">
            
            <div class="pull-right">
                <?= Html::a(Yii::t('app', 'Enable'), ['enable'], [
                    'id' => 'btn-enable', 'class' => 'btn btn-success btn-flat'
                ]) ?>
                <?= ' ' . Html::a(Yii::t('app', 'Disabled'), ['disable'], [
                    'id' => 'btn-disable', 'class' => 'btn btn-danger btn-flat'
                ]) ?>
                <?= ' ' . Html::a(Yii::t('app', '{Pass}{Certificate}', [
                    'Pass' => Yii::t('app', 'Pass'), 'Certificate' => Yii::t('app', 'Certificate')
                ]), ['pass'], ['id' => 'btn-pass', 'class' => 'btn btn-primary btn-flat']) ?>
            </div>

        </div>
    
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                [
                    'class' => 'yii\grid\CheckboxColumn',
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
                    'attribute' => 'nickname',
                    'label' => Yii::t('app', 'Real Name'),
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
                [
                    'attribute' => 'username',
                    'label' => Yii::t('app', 'User Account Number'),
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
                [
                    'attribute' => 'sex',
                    'label' => Yii::t('app', 'Sex'),
                    'value' => function($model){
                        return User::$sexName[$model->sex];
                    },
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
                [
                    'attribute' => 'phone',
                    'label' => Yii::t('app', 'Contact Way'),
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
                [
                    'attribute' => 'company',
                    'label' => Yii::t('app', 'Companies'),
                    'value' => function($model){
                        return !empty($model->profile) ? $model->profile->company : null;
                    },
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
                [
                    'attribute' => 'department',
                    'label' => Yii::t('app', 'Departments'),
                    'value' => function($model){
                        return !empty($model->profile) ? $model->profile->department : null;
                    },
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
                [
                    'attribute' => 'status',
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Status'),
                    'value' => function($model){
                        if($model->status > User::STATUS_STOP){
                            return User::$statusIs[$model->status];
                        }else{
                            return '<span class="text-danger">' . User::$statusIs[$model->status] . '</span>';
                        }
                    },
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
                [
                    'attribute' => 'is_certificate',
                    'format' => 'raw',
                    'label' => Yii::t('app', 'Is Certificate'),
                    'value' => function($model){
                        if(!empty($model->profile)){
                            if($model->profile->is_certificate){
                                return UserProfile::$certificateIs[$model->profile->is_certificate];
                            }else{
                                return '<span class="text-danger">' . UserProfile::$certificateIs[$model->profile->is_certificate] . '</span>';
                            }
                        }else{
                            return null;
                        }
                    },
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
                [
                    'attribute' => 'created_at',
                    'label' => Yii::t('app', 'Tegistration Time'),
                    'value' => function($model){
                        return date('Y-m-d H:i', $model->created_at);
                    },
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
                            
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'buttons' => [
                        'update' => function($url, $model){
                            return Html::a(Yii::t('app', 'Edit'), ['update', 'id' => $model->id], [
                                'class' => 'btn btn-primary'
                            ]);
                        },
                    ],
                    'headerOptions' => [
                        'style' => [
                            'width' => '65px',
                            'padding' => '8px 4px'
                        ],
                    ],
                    'contentOptions' => [
                        'style' => [
                            'padding' => '8px 4px'
                        ],
                    ],

                    'template' => '{update}',
                ],
            ],
        ]); ?>
        
    </div>
        
</div>

<?php
$msg = Yii::t('app', 'Please select at least one.');    // 消息提示
$js = <<<JS
        
    // 启用、停用、通过
    $('#btn-enable, #btn-disable, #btn-pass').click(function(e){
        e.preventDefault();
        var val = [],
            checkBoxs = $('input[name="selection[]"]'), 
            url = $(this).attr("href"),
            text = $(this).text();
        // 循环组装用户id
        for(i in checkBoxs){
            if(checkBoxs[i].checked){
               val.push(checkBoxs[i].value);
            }
        }
        if(val.length > 0){
            if(confirm("你确定要【"+text+"】选中的用户?") == true){
                window.location.href = url + "?ids=" + val;
            }
        }else{
            alert("{$msg}");
        }
    });    
    
JS;
    $this->registerJs($js,  View::POS_READY);
?>