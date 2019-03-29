<?php

use common\models\media\MediaAttribute;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttribute */

$this->title = Yii::t('app', "{Update}{Attribute}", [
    'Update' => Yii::t('app', 'Update'), 'Attribute' => Yii::t('app', 'Attribute')
]);

?>
<div class="media-attribute-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
