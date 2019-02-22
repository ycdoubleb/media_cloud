<?php

/**
 * 单独统计子页面
 * 购买人数据页面
 */

?>

<div class="order-amount">
    <div class="statistics-img media-img">
        <img src="/imgs/data.png"/>
    </div>
    <div class="statistics-num">
        <p>购买素材数量</p>
        <span><?= $purchaser['media_num'];?></span>
    </div>
</div>

<div class="order-amount">
    <div class="statistics-img amount-img">
        <img src="/imgs/yuan.png"/>
    </div>
    <div class="statistics-num">
        <p>总支出金额</p>
        <span><?= Yii::$app->formatter->asCurrency(empty($purchaser['order_amount']) ? 0 : $purchaser['order_amount']);?></span>
    </div>
</div>