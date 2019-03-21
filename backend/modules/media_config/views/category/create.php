<?php

use common\models\media\MediaCategory;
use yii\web\View;

/* @var $this View */
/* @var $model MediaCategory */

$this->title = Yii::t('app', "{Create}{Categorys}", [
    'Create' => Yii::t('app', 'Create'), 'Categorys' => Yii::t('app', 'Categorys')
]);

?>
<div class="media-category-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
