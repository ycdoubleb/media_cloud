<?php

use common\models\media\Dir;
use yii\web\View;

/* @var $this View */
/* @var $model Dir */

$this->title = Yii::t('app', '{Create}{Dir}', [
    'Create' => Yii::t('app', 'Create'), 'Dir' => Yii::t('app', 'Dir')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '{Storage}{Dir}', [
    'Storage' => Yii::t('app', 'Storage'), 'Dir' => Yii::t('app', 'Dir')
]), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="dir-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
