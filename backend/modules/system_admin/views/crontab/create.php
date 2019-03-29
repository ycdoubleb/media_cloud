<?php

use common\models\Crontab;
use yii\web\View;

/* @var $this View */
/* @var $model Crontab */

$this->title = Yii::t('app', '{Create}{Crontabs}', [
    'Create' => Yii::t('app', 'Create'), 'Crontabs' => Yii::t('app', 'Crontabs')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Crontabs}{List}', [
    'Crontabs' => Yii::t('app', 'Crontabs'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
