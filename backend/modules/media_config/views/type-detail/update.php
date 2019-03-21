<?php

use common\models\media\MediaTypeDetail;
use yii\web\View;

/* @var $this View */
/* @var $model MediaTypeDetail */

$this->title = Yii::t('app', "{Update}{Type}{Suffix}", [
    'Update' => Yii::t('app', 'Update'), 'Type' => Yii::t('app', 'Type'), 'Suffix' => Yii::t('app', 'Suffix')
]);

?>
<div class="media-type-detail-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
