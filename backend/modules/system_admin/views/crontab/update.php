<?php

use common\models\Crontab;
use yii\web\View;

/* @var $this View */
/* @var $model Crontab */

$this->title = Yii::t('app', '{Update}{Crontabs}', [
    'Update' => Yii::t('app', 'Update'), 'Crontabs' => Yii::t('app', 'Crontabs'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Crontabs}{List}', [
    'Crontabs' => Yii::t('app', 'Crontabs'), 'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="crontab-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
