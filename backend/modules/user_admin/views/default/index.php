<?php

use common\models\AdminUser;
use yii\grid\ActionColumn;
use yii\grid\CheckboxColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;


/* @var $this View */
/* @ver $dataProvider ActiveDataProvider */
/* @var $model AdminUser */

$this->title = '管理用户';
?>
<div class="user-index">
    <p>
        <?= Html::a('新增', ['create'], ['id' => 'btn-addUser', 'class' => 'btn btn-success']) ?>
        <?= Html::a('同步GUID', ['tongbu'], ['class' => 'btn btn-info']) ?>
    </p>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => CheckboxColumn::className()],
            'username',
            'nickname',
            'email',
            'guid',
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->created_at);
                }
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', 'View'),
                            'aria-label' => Yii::t('yii', 'View'),
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['view', 'id' => $key], $options);
                    }
                ]
            ]
        ],
        'tableOptions' => ['class' => 'table table-striped']
    ]);
    ?>

</div>

<!--加载模态框-->
<?= $this->render('____model'); ?>

<?php
$js = <<<JS
    
    // 弹出用户添加面板
    $('#btn-addUser').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>