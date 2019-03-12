<?php

/**
 * 单独统计子页面
 * 素材数据页面
 */

?>

<div class="order-amount">
    <div class="statistics-img quote-img">
        <img src="/imgs/quote.png"/>
    </div>
    <div class="statistics-num">
        <p>引用次数</p>
        <span><?= $media['quote_num'];?></span>
    </div>
</div>

<div class="order-amount">
    <div class="statistics-img amount-img">
        <img src="/imgs/yuan.png"/>
    </div>
    <div class="statistics-num">
        <p>总收入金额</p>
        <span><?= Yii::$app->formatter->asCurrency(empty($media['order_amount']) ? 0 : $media['order_amount']);?></span>
    </div>
</div>

<div class="order-amount" style="margin-right: 0px;">
    <div class="statistics-img play-img">
        <img src="/imgs/play.png"/>
    </div>
    <div class="statistics-num">
        <p>总学习量</p>
        <span><?= empty($media['click_num']) ? 0 : $media['click_num'];?></span>
    </div>
</div>