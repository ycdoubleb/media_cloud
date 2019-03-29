<?php

use common\models\Config;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model Config */

$this->title = Yii::t('app', "{Update}{Configs}", [
    'Update' => Yii::t('app', 'Update'),
    'Configs' => Yii::t('app', 'Configs'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Configs}{List}', [
    'Configs' => Yii::t('app', 'Configs'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="config-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
