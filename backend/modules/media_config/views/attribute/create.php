<?php

use common\models\media\MediaAttribute;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model MediaAttribute */

$this->title = Yii::t('app', '{Create}{Attribute}', [
    'Create' => Yii::t('app', 'Create'), 'Attribute' => Yii::t('app', 'Attribute')
]);

?>
<div class="media-attribute-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
