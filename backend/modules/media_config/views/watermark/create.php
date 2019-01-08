<?php

use common\models\Watermark;
use yii\web\View;

/* @var $this View */
/* @var $model Watermark */

$this->title = Yii::t('app', '{Create}{Watermark}', [
    'Create' => Yii::t('app', 'Create'), 'Watermark' => Yii::t('app', 'Watermark')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Watermark'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="watermark-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
