<?php

use common\models\media\MediaAttribute;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\web\View;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $model MediaAttribute */

$this->title =  Yii::t('app', "{Attribute}{Detail}", [
    'Attribute' => Yii::t('app', 'Attribute'), 'Detail' => Yii::t('app', 'Detail')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Attribute}{List}', [
    'Attribute' => Yii::t('app', 'Attribute'), 'List' => Yii::t('app', 'List') 
]), 'url' => ['index', 'category_id' => $model->category_id]];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);
?>
<div class="media-attribute-view">
    
    <!--基本信息-->
    <div class="panel pull-left">
        
        <div class="title"><?= Yii::t('app', 'Basic Info') ?></div>
        
        <?= DetailView::widget([
            'model' => $model,
            'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
            'attributes' => [
                [
                    'attribute' => 'name',
                    'label' => Yii::t('app', '{Attribute}{Name}', [
                        'Attribute' => Yii::t('app', 'Attribute'), 'Name' => Yii::t('app', 'Name')
                    ]),
                ],
                [
                    'label' => Yii::t('app', 'Category For Belong'),
                    'value' => !empty($model->category_id) ? $model->category->name : null,
                ],
                [
                    'label' => Yii::t('app', 'Is Del'),
                    'value' => !$model->is_del ? '否' : '是',
                ],
                [
                    'label' => Yii::t('app', '{Input}{Type}', [
                        'Input' => Yii::t('app', 'Input'), 'Type' => Yii::t('app', 'Type')
                    ]),
                    'value' => MediaAttribute::$inputTypeMap[$model->input_type],
                ],
                [
                    'attribute' => 'value_length',
                    'label' => Yii::t('app', '{Value}{Length}', [
                        'Value' => Yii::t('app', 'Value'), 'Length' => Yii::t('app', 'Length')
                    ]),
                ],
                [
                    'attribute' => 'is_required',
                    'value' => $model->is_required ? '是' : '否'
                ],
                [
                    'label' => Yii::t('app', 'Is Search'),
                    'value' => $model->index_type ? '是' : '否'
                ],
            ],
        ]) ?>
        
    </div>

    <!--属性值-->
    <div class="panel pull-left">
        
        <div class="title">
            
            <div class="pull-left"><?= Yii::t('app', 'Attribute Candidate Value') ?></div>
            
            <div class="btngroup pull-right">
                <?= Html::a(Yii::t('app', 'Add'), ['attribute-value/create', 'attribute_id' => $model->id], 
                    ['id' => 'btn-addValue', 'class' => 'btn btn-primary btn-flat']); ?>
            </div>
            
        </div>
        
        <?= $this->render('/attribute-value/index', [
            'filters' => $filters,
            'totalCount' => $totalCount,
            'dataProvider' => $dataProvider,
        ]) ?>
        
    </div>    
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出素材属性值面板
    $('#btn-addValue').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>