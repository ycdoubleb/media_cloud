<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\media\Acl */

$this->title = Yii::t('app', 'Create Acl');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Acls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="acl-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
