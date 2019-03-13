<?php

use backend\modules\statistics\assets\StatisticsModuleAsset;
use common\widgets\charts\ChartAsset;
use yii\web\View;

/* @var $this View */

StatisticsModuleAsset::register($this);
ChartAsset::register($this);

$this->title = Yii::t('app', 'Total Statistics');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="all-statistics-index">
    <div class="top-info col-lg-12">
        <div class="media-num col-lg-4">
            <div class="statistics-img media-img">
                <img src="/imgs/data.png"/>
            </div>
            <div class="statistics-num">
                <p>素材总数量</p>
                <span><?= $total_media_num['total_media_num'];?></span>
            </div>
        </div>
        <div class="order-amount col-lg-4">
            <div class="statistics-img amount-img">
                <img src="/imgs/yuan.png"/>
            </div>
            <div class="statistics-num">
                <p>总收入金额</p>
                <span><?= empty($total_order_amount['total_order_amount']) ? 0 : $total_order_amount['total_order_amount'];?></span>
            </div>
        </div>
        <div class="visit-num col-lg-4">
            <div class="statistics-img visit-img">
                <img src="/imgs/play.png"/>
            </div>
            <div class="statistics-num">
                <p>总学习量</p>
                <span><?= $total_visit_num;?></span>
            </div>
        </div>
    </div>
    
    <div class="statistics-view col-lg-12">
        <div class="mc-title">素材类型占比</div>
        <div class="mc-panel">
            <div id="statisticsChart" class="chart" style="height: 500px;"></div>
        </div>
    </div>
</div>

<?php
$charts = json_encode($statistics_chart);

$js = <<<JS
        
    // 统计结果
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 个) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}个 ({d}%)'},document.getElementById('statisticsChart'), $charts);
JS;
$this->registerJs($js, View::POS_READY);
?>