<?php

/**
 * 单独统计子页面
 * 运营人数据页面
 */

?>

<div class="order-amount">
    <div class="statistics-img media-img">
        <img src="/imgs/data.png"/>
    </div>
    <div class="statistics-num">
        <p>素材数量</p>
        <span><?= $operator['media_num'];?></span>
    </div>
</div>

<div class="order-amount">
    <div class="statistics-img amount-img">
        <img src="/imgs/yuan.png"/>
    </div>
    <div class="statistics-num">
        <p>总收入金额</p>
        <span><?= Yii::$app->formatter->asCurrency(empty($operator['order_amount']) ? 0 : $operator['order_amount']);?></span>
    </div>
</div>