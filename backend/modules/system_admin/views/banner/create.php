<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Banner */

$this->title = Yii::t('app', '{Create}Banner',[
    'Create' => Yii::t('app', 'Create'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banner{List}', [
    'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="banner-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
