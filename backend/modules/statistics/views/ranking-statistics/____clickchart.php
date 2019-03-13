<?php

use yii\web\View;

/**
 * 素材学习量子页面
 * 右侧饼图部分
 */

?>
<div class="order-amount">
    <div class="statistics-img click-img">
        <img src="/imgs/play.png"/>
    </div>
    <div class="statistics-num">
        <p>总学习量</p>
        <span><?= $click['totalClick'];?></span>
    </div>
</div>

<div class="order-amount">
    <div class="statistics-img click-img">
        <img src="/imgs/play.png"/>
    </div>
    <div class="statistics-num">
        <p>前20名学习量</p>
        <span><?= $click['limitClick'];?></span>
    </div>
</div>

<div id="statisticsChart" class="chart" style="height: 500px;"></div>

<?php
$charts = json_encode($click['chartsData']);

$js = <<<JS
        
    // 统计结果
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 次) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}次 ({d}%)'},document.getElementById('statisticsChart'), $charts);
JS;
$this->registerJs($js, View::POS_READY);
?>