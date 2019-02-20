<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use backend\modules\operation_admin\searchs\PlayApproveSearch;
use common\models\order\PlayApprove;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $searchModel PlayApproveSearch */
/* @var $dataProvider ActiveDataProvider */

OperationModuleAsset::register($this);

$this->title = Yii::t('app', '{Order}{Auditing}', [
    'Order' => Yii::t('app', 'Order'), 'Auditing' => Yii::t('app', 'Auditing')
]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="play-approve-index">
    
    <?= $this->render('_search', [
        'model' => $searchModel,
        'createdByMap' => $createdByMap,
        'handledByMap' => $handledByMap
    ]) ?>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => "{items}\n{summary}\n{pager}",
        'summaryOptions' => ['class' => 'hidden'],
        'pager' => [
            'options' => ['class' => 'hidden']
        ],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'headerOptions' => [
                    'style' => [
                        'width' => '30px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],

            [
                'label' => Yii::t('app', '{Order}{Name}', [
                    'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                ]),
                'value' => function($model){
                    return !empty($model->order_id) ? $model->order->order_name : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '150px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', 'Order Sn'),
                'value' => function($model){
                    return !empty($model->order_id) ? $model->order->order_sn : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '155px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', '{Payable}{Amount}', [
                    'Payable' => Yii::t('app', 'Payable'), 'Amount' => Yii::t('app', 'Amount')
                ]),
                'value' => function($model){
                    return !empty($model->order_id) ? Yii::$app->formatter->asCurrency($model->order->order_amount) : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '80px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', 'Purchaser'),
                'value' => function($model){
                    return !empty($model->created_by) ? $model->createdBy->nickname : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '66px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', '{Payment}{Voucher}', [
                    'Payment' => Yii::t('app', 'Payment'), 'Voucher' => Yii::t('app', 'Voucher')
                ]),
                'format' => 'raw',
                'value' => function($model){
                    return Html::a(Html::img($model->certificate_url, ['width' => 63, 'height' => 44]), null, [
                        'title' => '单击放大查看', 'style' => 'cursor:pointer;', 'onclick' => 'amplifyPicture($(this));'
                    ]);
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '75px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', '{Payment}{Time}', [
                    'Payment' => Yii::t('app', 'Payment'), 'Time' => Yii::t('app', 'Time')
                ]),
                'value' => function($model){
                    return !empty($model->order_id) && $model->order->play_at > 0 ? date('Y-m-d H:i', $model->order->play_at) : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '76px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                        'font-size' => '13px'
                    ],
                ]
            ],
            [
                'attribute' => 'content',
                'label' => Yii::t('app', '{Payment}{Illustration}', [
                    'Payment' => Yii::t('app', 'Payment'), 'Illustration' => Yii::t('app', 'Illustration')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '155px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', '{Auditing}{Result}', [
                    'Auditing' => Yii::t('app', 'Auditing'), 'Result' => Yii::t('app', 'Result')
                ]),
                'value' => function($model){
                    return $model->status == 1 ? PlayApprove::$resultName[$model->result] : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '66px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', 'Verifier'),
                'value' => function($model){
                    return !empty($model->handled_by) ? $model->handledBy->nickname : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '66px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],
            [
                'label' => Yii::t('app', '{Auditing}{Time}', [
                    'Auditing' => Yii::t('app', 'Auditing'), 'Time' => Yii::t('app', 'Time')
                ]),
                'value' => function($model){
                    return !empty($model->handled_at) ? date('Y-m-d H:i', $model->handled_at) : null;
                },
                'headerOptions' => [
                    'style' => [
                        'width' => '76px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                        'font-size' => '13px'
                    ],
                ]
            ],
            [
                'attribute' => 'feedback',
                'label' => Yii::t('app', '{Feedback}{Info}', [
                    'Feedback' => Yii::t('app', 'Feedback'), 'Info' => Yii::t('app', 'Info')
                ]),
                'headerOptions' => [
                    'style' => [
                        'width' => '155px',
                        'padding' => '8px 2px',
                    ]
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ]
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'buttons' => [
                    'approve' => function($url, $model){
                        return Html::a(Yii::t('app', 'Auditing'), ['view', 'id' => $model->id], ['class' => 'btn btn-primary']);
                    },
                    'order' => function($url, $model){
                        return ' '. Html::a(Yii::t('app', 'Order'), ['order/view', 'id' => $model->order_id], ['class' => 'btn btn-default']);
                    },
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '117px',
                        'padding' => '8px 2px',
                    ],
                ],
                'contentOptions' => [
                    'style' => [
                        'padding' => '8px 2px',
                    ],
                ],

                'template' => '{approve}{order}',
            ],
        ],
    ]); ?>
    
    <?php
        $page = ArrayHelper::getValue($filters, 'page', 1);
        $pageCount = ceil($totalCount / 10);
        if($pageCount >= 2){
            echo '<div class="summary">' . 
                    '第 <b>' . (($page * 10 - 10) + 1) . '</b>-<b>' . ($page != $pageCount ? $page * 10 : $totalCount) .'</b> 条，总共 <b>' . $totalCount . '</b> 条数据。' .
                '</div>';

            echo LinkPager::widget([  
                'pagination' => new Pagination([
                    'totalCount' => $totalCount,
                    'pageSize' => 10
                ]),  
                'maxButtonCount' => 5
            ]);
        }
    ?>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<script type="text/javascript">
    
    /**
     * 放大图片查看
     * @param {object} _this
     * @returns {undefined}
     */
    function amplifyPicture(_this){
        var url = _this.children('img').attr('src');
        $('#myModalBody').html($('<img src="" width="100%"/>').attr('src', url));
        $('.myModal').modal("show");
    }
    
</script>