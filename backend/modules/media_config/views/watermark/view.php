<?php

use common\models\Watermark;
use common\widgets\watermark\WatermarkAsset;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model Watermark */

YiiAsset::register($this);
WatermarkAsset::register($this);

$this->title =  Yii::t('app', "{Watermark}{Detail}", [
    'Watermark' => Yii::t('app', 'Watermark'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Watermark}{List}', [
    'Watermark' => Yii::t('app', 'Watermark'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="watermark-view">

    <div class="panel pull-left">
        
        <div class="title">
            <?= Yii::t('app', 'Basic Info') ?>
            
            <div class="pull-right">
                
                <?php 
                    // 更新按钮
                    echo Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']);
                    // 删除按钮
                    echo ' '. Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                        'class' => 'btn btn-danger btn-flat',
                        'data' => [
                            'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                            'method' => 'post',
                        ],
                    ]);
                    
                ?>
                
            </div>
            
        </div>
        
        <?= DetailView::widget([
            'model' => $model,
            'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
            'attributes' => [
                'id',
                [
                    'attribute' => 'name',
                    'label' => Yii::t('app', '{Watermark}{Name}', [
                        'Watermark' => Yii::t('app', 'Watermark'), 'Name' => Yii::t('app', 'Name')
                    ]),
                ],
                [
                    'attribute' => 'refer_pos',
                    'label' => Yii::t('app', 'Refer Pos'),
                    'value' => Watermark::$referPosMap[$model->refer_pos],
                ],
                'width',
                'height',
                [
                    'attribute' => 'dx',
                    'label' => Yii::t('app', 'Dx'),
                ],
                [
                    'attribute' => 'dy',
                    'label' => Yii::t('app', 'Dy'),
                ],
                [
                    'attribute' => 'is_del',
                    'label' => Yii::t('app', 'Is Del'),
                    'value' => !$model->is_del ? '否' : '是'
                ],
                [
                    'attribute' => 'is_selected',
                    'label' => Yii::t('app', 'Is Selected'),
                   'value' => !$model->is_selected ? '否' : '是'
                ],
                [
                    'attribute' => 'created_at',
                    'format' => 'raw',
                    'value' => date('Y-m-d H:i', $model->created_at),
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => 'raw',
                    'value' => date('Y-m-d H:i', $model->updated_at),
                ],
                [
                    'label' => Yii::t('app', 'Preview'),
                    'format' => 'raw',
                    'value' => '<div id="preview-watermark" class="preview"></div>',
                ],
            ],
        ]) ?>
        
    </div>
    
</div>

<?php
$js = <<<JS
    var watermark;    
        
    //初始化组件
    watermark = new wate.Watermark();
    
    //添加一个水印
    watermark.addWatermark({
        refer_pos: "{$model->refer_pos}", url: "{$model->url}",
        width: "{$model->width}", height: "{$model->height}",
        dx: "{$model->dx}", dy: "{$model->dy}"
    });
JS;
    $this->registerJs($js,  View::POS_READY);
?>