<?php

use yii\web\View;

/**
 * 运营人收入金额子页面
 * 右侧饼图部分
 */

?>
<div class="order-amount">
    <div class="statistics-img amount-img">
        <img src="/imgs/yuan.png"/>
    </div>
    <div class="statistics-num">
        <p>总收入金额</p>
        <span><?= Yii::$app->formatter->asCurrency($operator['totalAmount']);?></span>
    </div>
</div>

<div id="statisticsChart" class="chart" style="height: 500px;"></div>

<?php
$charts = json_encode($operator['chartsData']);

$js = <<<JS
        
    // 统计结果
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 元) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}元 ({d}%)'},document.getElementById('statisticsChart'), $charts);
JS;
$this->registerJs($js, View::POS_READY);
?>