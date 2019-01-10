<?php

use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->title = Yii::t('app', '{Personal}{Set}', [
    'Personal' => Yii::t('app', 'Personal'),
    'Set' => Yii::t('app', 'Set'),
]);

ModuleAssets::register($this);

?>
<div class="user-update main">
    <div class="mc-title">
        <span><?= $this->title;?></span>
    </div>
    <div class="choice-panel">
        <div class="mc-panel clear-margin">
            <div class="mc-tabs">
                <ul class="list-unstyled">
                    <li id="base">
                        <?= Html::a('基本信息', array_merge(['setting'], array_merge($filter, ['set' => 'base']))) ?>
                    </li>
                    <li id="other">
                        <?= Html::a('其他信息', array_merge(['setting'], array_merge($filter, ['set' => 'other']))) ?>
                    </li>
                </ul>
            </div>
            <?php
                if($sets == 'base'){
                    echo $this->render('____baseform', [
                        'userModel' => $userModel,
                    ]);
                } else {       
                    echo $this->render('____otherform', [
                        'peofileModel' => $peofileModel,
                    ]);
                }
            ?>
        </div>
    </div>
</div>

<?php
$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$sets]").addClass('active');
        
JS;
$this->registerJs($js, View::POS_READY);
?>