<?php

use common\models\media\MediaTypeDetail;
use yii\web\View;

/* @var $this View */
/* @var $model MediaTypeDetail */

$this->title = Yii::t('app', '{Create}{{Type}{Suffix}', [
    'Create' => Yii::t('app', 'Create'), 'Type' => Yii::t('app', 'Type'), 'Suffix' => Yii::t('app', 'Suffix')
]);

?>
<div class="media-type-detail-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
