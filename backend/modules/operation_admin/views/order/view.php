<?php

use backend\modules\operation_admin\assets\OperationModuleAsset;
use common\models\media\MediaTypeDetail;
use common\models\order\Order;
use common\models\order\PlayApprove;
use common\utils\DateUtil;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Order */

YiiAsset::register($this);
OperationModuleAsset::register($this);

$this->title = Yii::t('app', "{Orders}{Detail}", [
    'Orders' => Yii::t('app', 'Orders'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Orders}{List}', [
    'Orders' => Yii::t('app', 'Orders'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$mediaTypeIds = ArrayHelper::getColumn($goodsDataProvider->allModels, 'media.type_id');
$iconMap = ArrayHelper::map(MediaTypeDetail::getMediaTypeDetailByTypeId($mediaTypeIds, false), 'name', 'icon_url');

?>
<div class="order-view">

    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
            <a href="#basics" role="tab" data-toggle="tab" aria-controls="basics" aria-expanded="true">
                <?= Yii::t('app', 'Basic Info') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a href="#pay" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', 'Offline Payment') ?>
            </a>
        </li>
        <li role="presentation" class="">
            <a href="#goods" role="tab" data-toggle="tab" aria-controls="config" aria-expanded="false">
                <?= Yii::t('app', '{Medias}{List}', [
                    'Medias' => Yii::t('app', 'Medias'), 'List' => Yii::t('app', 'List')
                ]) ?>
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
                        'label' => Yii::t('app', '{Orders}{Name}', [
                            'Orders' => Yii::t('app', 'Orders'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'value' => $model->order_name
                    ],
                    [
                        'label' => Yii::t('app', 'Orders Sn'),
                        'value' => $model->order_sn
                    ],
                    [
                        'label' => Yii::t('app', '{Orders}{Status}', [
                            'Orders' => Yii::t('app', 'Orders'), 'Status' => Yii::t('app', 'Status')
                        ]),
                        'value' => Order::$orderStatusName[$model->order_status]
                    ],
                    [
                        'label' => Yii::t('app', '{Goods}{Num}', [
                            'Goods' => Yii::t('app', 'Goods'), 'Num' => Yii::t('app', 'Num')
                        ]),
                        'value' => $model->goods_num
                    ],
                    [
                        'label' => Yii::t('app', 'Goods Total Price'),
                        'value' => Yii::$app->formatter->asCurrency($model->goods_amount)
                    ],
                    [
                        'label' => Yii::t('app', 'Payable Amount'),
                        'value' => Yii::$app->formatter->asCurrency($model->order_amount)
                    ],
                    [
                        'label' => Yii::t('app', 'Purchaser'),
                        'value' => !empty($model->created_by) ? $model->createdBy->nickname : null
                    ],
                    [
                        'label' => Yii::t('app', 'Payment Status'),
                        'value' => Order::$playStatusName[$model->play_status]
                    ],
                    [
                        'label' => Yii::t('app', 'Payment Mode'),
                        'value' => !empty($model->play_code) ? Order::$playCodeMode[$model->play_code] : null
                    ],
                    [
                        'label' => Yii::t('app', 'Place Order Time'),
                        'value' => $model->created_at > 0 ? date('Y-m-d H:i', $model->created_at) : null
                    ],
                    [
                        'label' => Yii::t('app', 'Payment Time'),
                        'value' => $model->play_at > 0 ? date('Y-m-d H:i', $model->play_at) : null
                    ],
                    [
                        'label' => Yii::t('app', '{Confirm}{Time}', [
                            'Confirm' => Yii::t('app', 'Confirm'), 'Time' => Yii::t('app', 'Time')
                        ]),
                        'value' => $model->confirm_at > 0 ? date('Y-m-d H:i', $model->confirm_at) : null
                    ],                   
                    'user_note',
                ],
            ]) ?>
        
        </div>
        
        <!--线下支付-->
        <div role="tabpanel" class="tab-pane fade" id="pay" aria-labelledby="pay-tab">
            
            <?= GridView::widget([
                'dataProvider' => $playDataProvider,
                'layout' => "{items}\n{summary}\n{pager}",
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => [
                                'width' => '50px',
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
                        'label' => Yii::t('app', 'Payable Amount'),
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
                        'label' => Yii::t('app', 'Payment Voucher'),
                        'format' => 'raw',
                        'value' => function($model){
                            return Html::a(Html::img($model->certificate_url, ['width' => 63, 'height' => 44]), null, [
                                'title' => Yii::t('app', 'Click to enlarge view'), 
                                'style' => 'cursor:pointer;', 
                                'onclick' => 'amplifyPicture($(this));'
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
                        'label' => Yii::t('app', 'Payment Time'),
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
                        'label' => Yii::t('app', 'Payment Description'),
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
                        'label' => Yii::t('app', '{Approves}{Result}', [
                            'Approves' => Yii::t('app', 'Approves'), 'Result' => Yii::t('app', 'Result')
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
                        'label' => Yii::t('app', 'Approver'),
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
                        'label' => Yii::t('app', 'Approves Time'),
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
                ],
            ]); ?>
            
        </div>
        
        <!--素材列表-->
        <div role="tabpanel" class="tab-pane fade" id="goods" aria-labelledby="goods-tab">
            
            <?= GridView::widget([
                'dataProvider' => $goodsDataProvider,
                'layout' => "{items}\n{summary}\n{pager}",
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'headerOptions' => [
                            'style' => [
                                'width' => '50px',
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
                        'attribute' => 'goods_id',
                        'label' => Yii::t('app', '{Medias}{Number}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Number' => Yii::t('app', 'Number')
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
                    [
                        'label' => Yii::t('app', 'Thumb Img'),
                        'format' => 'raw',
                        'value' => function($model) use($iconMap){
                            if(!empty($model->goods_id)){
                                if($model->media->cover_url != null){
                                    $cover_url = $model->media->cover_url;
                                }else if(isset($iconMap[$model->media->ext])){
                                    $cover_url = $iconMap[$model->media->ext];
                                }else{
                                    $cover_url = '';
                                }
                                return Html::img($cover_url, ['width' => 87, 'height' => 74]);
                            }else{
                                return null;
                            }
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '96px',
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
                        'label' => Yii::t('app', '{Medias}{Name}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Name' => Yii::t('app', 'Name')
                        ]),
                        'value' => function($model){
                            return !empty($model->goods_id) ? $model->media->name : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '180px',
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
                        'label' => Yii::t('app', 'Type'),
                        'value' => function($model){
                            return !empty($model->goods_id) ? $model->media->mediaType->name : null;
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
                        'label' => Yii::t('app', 'Duration'),
                        'value' => function($model){
                            return !empty($model->goods_id) ? DateUtil::intToTime($model->media->duration, ':', true) : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '76px',
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
                        'label' => Yii::t('app', 'Size'),
                        'value' => function($model){
                            return !empty($model->goods_id) ? Yii::$app->formatter->asShortSize($model->media->size) : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '86px',
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
                        'label' => Yii::t('app', 'Operator'),
                        'value' => function($model){
                            return !empty($model->goods_id) && !empty($model->media->owner_id) ? $model->media->owner->nickname : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '76px',
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
                        'label' => Yii::t('app', '{Medias}{Price}', [
                            'Medias' => Yii::t('app', 'Medias'), 'Price' => Yii::t('app', 'Price')
                        ]),
                        'value' => function($model){
                            return !empty($model->goods_id) ? Yii::$app->formatter->asCurrency($model->media->price) : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '70px',
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
                        'label' => Yii::t('app', 'Tags'),
                        'value' => function($model){
                            return !empty($model->goods_id) ?  implode(',', ArrayHelper::getColumn($model->media->mediaTagRefs, 'tags.name')) : null;
                        },
                        'headerOptions' => [
                            'style' => [
                                'width' => '196px',
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
                        'buttons' => [
                            'media' => function($url, $model){
                                return Html::a('查看', ['/media_admin/media/view', 'id' => $model->goods_id], ['class' => 'btn btn-default']);
                            },
                        ],
                        'headerOptions' => [
                            'style' => [
                                'width' => '80px',
                                'padding' => '8px 2px',
                            ],
                        ],
                        'contentOptions' => [
                            'style' => [
                                'padding' => '8px 2px',
                            ],
                        ],

                        'template' => '{media}',
                    ],
                ],
            ]); ?>
            
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


<script type="text/javascript">
    
    /**
     * 放大图片查看
     * @param {object} _this
     * @returns {undefined}
     */
    function amplifyPicture(_this){
        var url = _this.children('img').attr('src');
        $(".modal-body").html("");
        $('.modal-body').html($('<img src="" width="100%"/>').attr('src', url));
        $('.myModal').modal("show");
    }
    
    
</script>