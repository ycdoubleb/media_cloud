<?php

use common\modules\rbac\models\searchs\AuthGroupSearch;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel AuthGroupSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app/rbac', 'Auth Groups');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="auth-group-index">

    <p>
        <?= Html::a(Yii::t('app', 'Create').Yii::t('app/rbac', 'Auth Group'), ['create'], [
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

            'name',
            'app',
            'sort_order',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'view' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'View'), ['view', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-default',
                            'onclick' => 'showModal($(this)); return false;'
                        ]);
                    },
                    'updata' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('app/rbac', 'Edit'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-primary',
                            'onclick' => 'showModal($(this)); return false;'
                        ]);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
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
                            
                'template' => '{view}{updata}{delete}',
            ],
        ],
    ]); ?>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>
