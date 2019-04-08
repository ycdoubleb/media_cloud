<?php

use common\models\media\MediaType;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model MediaType */

$this->title = Yii::t('app', "{Update}{Type}", [
    'Update' => Yii::t('app', 'Update'), 'Type' => Yii::t('app', 'Type')
]);

?>
<div class="media-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
