<?php

use kartik\widgets\Select2;
use common\modules\rbac\models\searchs\AuthItemSearch;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel AuthItemSearch */
/* @var $dataProvider ArrayDataProvider */

$this->title = Yii::t('app/rbac', 'Permission');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="permission-index rbac">
    <p>
        <?= Html::a(Yii::t('app', 'Create').Yii::t('app/rbac', 'Permission'), ['create'], [
            'class' => 'btn btn-success',
            'onclick' => 'showModal($(this)); return false;'
        ]) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => "{items}\n{summary}\n{pager}",  
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'group_id',  
                'headerOptions' => ['style' => 'width: 240px;'],
                'format' => 'raw',
                'value' => function ($model) use($authGroups) {
                    /* @var $model AuthItemSearch */
                    return isset($authGroups[$model->group_id]) ? $authGroups[$model->group_id] : null;
                },
                'filter' => Select2::widget([
                    //'value' => null,
                    'model' => $searchModel,
                    'attribute' => 'group_id',
                    'data' => $authGroups,
                    'hideSearch'=>true,
                    'options' => ['placeholder' => Yii::t('app', 'Select Placeholder')],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])
            ],
            'name',
            'description:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => [
                    'style' => 'width: 150px'
                ],
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        /* @var $model AuthItemSearch */
                        return ' ' . Html::a(Yii::t('app/rbac', 'Edit'), ['view', 'id' => $model->name], [
                                'id' => 'btn-updateCate', 'class' => 'btn btn-primary',
                            ]);
                    },
                    'delete' => function ($url, $model, $key) {
                        /* @var $model AuthItemSearch */
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->name], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => Yii::t('app', "{Are you sure}{Delete}【{$model->name}】？", [
                                    'Are you sure' => Yii::t('app', 'Are you sure '), 'Delete' => Yii::t('app', 'Delete')
                                ]),
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'template' => '{view} {delete}',
            ],
        ],
    ]); ?>

</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>
