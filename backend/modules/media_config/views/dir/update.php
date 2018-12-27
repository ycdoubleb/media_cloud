<?php

use common\models\media\Dir;
use yii\web\View;

/* @var $this View */
/* @var $model Dir */

$this->title = Yii::t('app', "{Update}{Dir}：{$model->name}", [
    'Update' => Yii::t('app', 'Update'), 'Dir' => Yii::t('app', 'Dir')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Storage}{Dir}', [
    'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="dir-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
