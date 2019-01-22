<?php

use common\models\media\MediaAction;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model MediaActiona */

$this->title = Yii::t('app', '{View}{Media}{Operate}{Info}', [
    'View' => Yii::t('app', 'View'), 'Media' => Yii::t('app', 'Media'),
    'Operate' => Yii::t('app', 'Operate'), 'Info' => Yii::t('app', 'Info')
]);

?>
<div class="media-view-action">    
    
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
    
                <?= DetailView::widget([
                    'model' => $model,
                    'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                    'attributes' => [
                        'id',
                        [
                            'label' => Yii::t('app', '{Order}{Name}', [
                                'Order' => Yii::t('app', 'Order'), 'Name' => Yii::t('app', 'Name')
                            ]),
                            'value' => !empty($model->order_id) ? $model->order->order_name : null
                        ],
                        [
                            'attribute' => 'title',
                            'label' => Yii::t('app', '{Operate}{Type}', [
                                'Operate' => Yii::t('app', 'Operate'), 'Type' => Yii::t('app', 'Type')
                            ]),
                        ],
                        [
                            'label' => Yii::t('app', 'Created By'),
                            'value' => !empty($model->created_by) ? $model->createdBy->nickname : null,
                        ],
                        [
                            'label' => Yii::t('app', 'Content'),
                            'format' => 'raw',
                            'value' => $model->content,
                        ],
                        'created_at:datetime',
                        'updated_at:datetime',
                    ],
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default btn-flat', 'data-dismiss' => 'modal', 'aria-label' => 'Close']) ?>
                
            </div>
                
       </div>
    </div>
        
</div>
