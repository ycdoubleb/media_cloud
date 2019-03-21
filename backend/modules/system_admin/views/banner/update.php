<?php

use common\models\Banner;
use yii\web\View;

/* @var $this View */
/* @var $model Banner */

$this->title = Yii::t('app', '{Update}Banner', [
    'Update' => Yii::t('app', 'Update'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Banner{List}', [
    'List' => Yii::t('app', 'List')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');

?>
<div class="banner-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
