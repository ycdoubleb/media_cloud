<?php

use common\models\searchs\MediaCategorySearch;
use common\utils\I18NUitl;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\LinkPager;

/* @var $this View */
/* @var $searchModel MediaCategorySearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = Yii::t('app', '{Medias}{Categorys}', [
    'Medias' => Yii::t('app', 'Medias'), 'Categorys' => Yii::t('app', 'Categorys')
]);
$this->params['breadcrumbs'][] = Yii::t('app', '{Categorys}{List}', [
    'Categorys' => Yii::t('app', 'Categorys'), 'List' => Yii::t('app', 'List')
]);
?>
<div class="media-category-index">
  
    <p>
        <?= Html::a(Yii::t('app', '{Create}{Categorys}', [
            'Create' => Yii::t('app', 'Create'), 'Categorys' => Yii::t('app', 'Categorys')
        ]), ['create'], ['id' => 'btn-addCate', 'class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
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
                        'width' => '50px',
                    ],
                ],
            ],

            [
                'attribute' => 'name',
                'label' => I18NUitl::t('app', '{Categorys}{Name}'),
                'headerOptions' => [
                    'style' => [
                        'width' => '1000px',
                    ],
                ],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'updata' => function ($url, $model){
                        return Html::a(Yii::t('yii', 'Update'), ['update', 'id' => $model->id], [
                            'id' => 'btn-updateCate', 'class' => 'btn btn-default'
                        ]);
                    },
                    'delete' => function ($url, $model){
                        return ' ' . Html::a(Yii::t('yii', 'Delete'), ['delete', 'id' => $model->id], [
                            'id' => 'btn-deleteCate', 'class' => 'btn btn-danger',
                            'data' => [
                                'pjax' => 0, 
                                'confirm' => I18NUitl::t('app', "{Are you sure you want to}{Delete}【{$model->name}】{Categorys}？"),
                                'method' => 'post',
                            ],
                        ]);
                    },
                ],
                'headerOptions' => [
                    'style' => [
                        'width' => '200px',
                    ],
                ],
                            
                'template' => '{updata}{delete}',
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

<?php
$js = <<<JS
    
    // 弹出素材类目面板
    $('#btn-addCate, #btn-updateCate').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>