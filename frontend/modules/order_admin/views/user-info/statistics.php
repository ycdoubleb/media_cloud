<?php

use common\widgets\charts\ChartAsset;
use frontend\modules\order_admin\assets\ModuleAssets;
use kartik\daterange\DateRangePicker;
use kartik\widgets\Select2;
use yii\web\View;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$this->title = Yii::t('app', '{Data}{Statistics}', [
    'Data' => Yii::t('app', 'Data'),
    'Statistics' => Yii::t('app', 'Statistics'),
]);

ModuleAssets::register($this);
ChartAsset::register($this);

?>

<div class="user-statistics main">
    
    <div class="mc-title">
        <span><?= $this->title;?></span>
    </div>
    
    <div class="mc-panel clear-margin">
        <div class="statistics-form mc-form">
            <form id="order-form" class="form-horizontal">
                <!--时间段搜索-->
                <div class="form-group field-order-confirm_at required">
                    <label class="col-lg-2 col-md-2 control-label" style="color: #999999; padding-top: 10px; text-align: left;">时间段支出金额:</label>
                    <div class="col-lg-4 col-md-4" style="padding-left: 0px;">
                        <?= DateRangePicker::widget([
                            'value' => $dateRange,
                            'name' => 'dateRange',
                            //'presetDropdown' => true,
                            'hideInput' => true,
                            'convertFormat' => true,
                            'pluginOptions' => [
                                'locale' => ['format' => 'Y-m-d'],
                                'allowClear' => true,
                            ],
                            'pluginEvents' => ['change' => 'function() { submitForm(); }']
                        ]);?>
                    </div>
                </div>
                <!--总支付金额 总购买资源数-->
                <div class="all-datas">
                <div class="pull-left">
                    <div class="statistics-img left-img">
                        <img src="/imgs/site/yuan.png"/>
                    </div>
                    <div class="statistics-num left-price">
                        <p>总支出金额</p>
                        <span><?= Yii::$app->formatter->asCurrency(empty($totalPay['total_price']) ? 0 : $totalPay['total_price']);?></span>
                    </div>
                </div>
                <div class="pull-right">
                    <div class="statistics-img right-img">
                        <img src="/imgs/site/data.png"/>
                    </div>
                    <div class="statistics-num right-num">
                        <p>总购买资源数</p>
                        <span><?= empty($totalPay['total_goods']) ? 0 : $totalPay['total_goods'];?></span>
                    </div>
                </div>
            </div>
                <!--年份搜索-->
                <div class="form-group field-order-confirm_at required" style="margin-bottom: 0px">
                    <label class="col-lg-2 col-md-2 control-label" style="color: #999999; padding-top: 10px; text-align: left;">年度月支出金额:</label>
                    <div class="col-lg-2 col-md-2" style="padding-left: 0px;">
                        <?= Select2::widget([
                            'name' => 'year',
                            'value' => $year,
                            'data' => $years,
                            'hideSearch' => true,
                            'pluginEvents' => ['change' => 'function(){ submitForm()}']
                        ]);?>
                    </div>
                </div>
            </form>
        </div>
        
        <!--统计结果-->
        <div id="chartCanvas" class="chart">
            
        </div>
    </div>
</div>

<?php
$dateStatistics = json_encode($dateStatistics);

$js = <<<JS
    /**
     * 提交表单
     */
    window.submitForm = function(){
        $('#order-form').submit();
    }

    // 统计结果
    new ccoacharts.ColumnBarChart({title:"",itemLabelFormatter:'{c}'},document.getElementById('chartCanvas'), $dateStatistics);
JS;
$this->registerJs($js, View::POS_READY);
?>