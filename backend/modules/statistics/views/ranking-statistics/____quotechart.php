<?php

use yii\web\View;

/**
 * 素材引用次数子页面
 * 右侧饼图部分
 */

?>
<div class="order-amount">
    <div class="statistics-img quote-img">
        <img src="/imgs/quote.png"/>
    </div>
    <div class="statistics-num">
        <p>总引用次数</p>
        <span><?= $quote['totalQuote'];?></span>
    </div>
</div>

<div class="order-amount">
    <div class="statistics-img quote-img">
        <img src="/imgs/quote.png"/>
    </div>
    <div class="statistics-num">
        <p>前20名引用次数</p>
        <span><?= $quote['limitQuote'];?></span>
    </div>
</div>

<div id="statisticsChart" class="chart" style="height: 500px;"></div>

<?php
$charts = json_encode($quote['chartsData']);

$js = <<<JS
        
    // 统计结果
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 次) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}次 ({d}%)'},document.getElementById('statisticsChart'), $charts);
JS;
$this->registerJs($js, View::POS_READY);
?>