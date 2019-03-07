<?php

use common\modules\rbac\components\Helper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
    
?>

<p>
    <?php
        if( Helper::checkRoute(Url::to(['edit-attribute']))){
            echo Html::a(Yii::t('app', 'Edit'), ['edit-attribute', 'id' => $model->id], [
                'id' => 'btn-editAttribute', 'class' => 'btn btn-primary']);
        }
    ?>
</p>

<table id="w1" class="table table-striped table-bordered detail-view">

    <tbody>
        <?php foreach ($attrDataProvider as $data): ?>
        <tr>
            <th class="detail-th"><?= $data['attr_name'] ?></th>
            <td class="detail-td"><?= $data['attr_value'] ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <th class="detail-th">
                <?= Yii::t('app', '{Media}{Tag}', [
                    'Media' => Yii::t('app', 'Media'), 'Tag' => Yii::t('app', 'Tag')
                ]) ?>
            </th>
            <td class="detail-td"><?= $model->tags ?></td>
        </tr>
    </tbody>

</table>

<?php
$js = <<<JS
   
    // 弹出素材编辑页面面板
    $('#btn-editAttribute').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
     
JS;
    $this->registerJs($js, View::POS_READY);
?>