<?php

use common\models\media\MediaType;
use yii\web\View;

/* @var $this View */
/* @var $model MediaType */

$this->title = Yii::t('app', '{Create}{Type}', [
    'Create' => Yii::t('app', 'Create'), 'Type' => Yii::t('app', 'Type')
]);

?>
<div class="media-type-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
