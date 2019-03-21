<?php

use common\models\media\MediaCategory;
use yii\web\View;

/* @var $this View */
/* @var $model MediaCategory */

$this->title = Yii::t('app', "{Update}{Categorys}", [
    'Update' => Yii::t('app', 'Update'), 'Categorys' => Yii::t('app', 'Categorys')
]);

?>
<div class="media-category-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
