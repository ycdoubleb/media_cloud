<?php

use frontend\modules\order_admin\assets\ModuleAssets;
use kartik\daterange\DateRangePicker;

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

?>

<div class="user-statistics main">
    
    <div class="mc-title">
        <span><?= $this->title;?></span>
    </div>
    
    <div class="mc-panel clear-margin">
        <div class="statistics-form mc-form">
            <form id="w0" class="form-horizontal" action="/order_admin/user-info/setting" method="post" enctype="multipart/form-data">
                <!--时间段搜索-->
                <div class="form-group field-order-confirm_at required">
                    <label class="col-lg-2 col-md-2 control-label" style="color: #999999; padding-top: 10px; text-align: left;">时间段支出金额:</label>
                    <div class="col-lg-4 col-md-4" style="padding-left: 0px;">
                        <?= DateRangePicker::widget([
                            'value'=>$dateRange,
                            'name' => 'dateRange',
                            //'presetDropdown' => true,
                            'hideInput' => true,
                            'convertFormat'=>true,
                            'pluginOptions'=>[
                                'locale'=>['format' => 'Y-m-d'],
                                'allowClear' => true,
//                                'ranges' => [
//                                    Yii::t('app', "Statistics-Prev-Week") => ["moment().startOf('week').subtract(1,'week')", "moment().endOf('week').subtract(1,'week')"],
//                                    Yii::t('app', "Statistics-This-Week") => ["moment().startOf('week')", "moment().endOf('week')"],
//                                    Yii::t('app', "Statistics-Prev-Month") => ["moment().startOf('month').subtract(1,'month')", "moment().endOf('month').subtract(1,'month')"],
//                                    Yii::t('app', "Statistics-This-Month") => ["moment().startOf('month')", "moment().endOf('month')"],
//                                    Yii::t('app', "First Season") => ["moment().startOf('Q').quarter(1,'quarter')","moment().endOf('Q').quarter(1,'quarter')"],
//                                    Yii::t('app', "Second Season") => ["moment().startOf('Q').quarter(2,'quarter')","moment().endOf('Q').quarter(2,'quarter')"],
//                                    Yii::t('app', "Third Season") => ["moment().startOf('Q').quarter(3,'quarter')","moment().endOf('Q').quarter(3,'quarter')"],
//                                    Yii::t('app', "Fourth Season") => ["moment().startOf('Q').quarter(4,'quarter')","moment().endOf('Q').quarter(4,'quarter')"],
//                                    Yii::t('app', "Statistics-First-Half-Year") => ["moment().startOf('year')", "moment().startOf('year').add(5,'month').endOf('month')"],
//                                    Yii::t('app', "Statistics-Next-Half-Year") => ["moment().startOf('year').add(6,'month')", "moment().endOf('year')"],
//                                    Yii::t('app', "Statistics-Full-Year") => ["moment().startOf('year')", "moment().endOf('year')"],
//                                ]
                            ],
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
                            <span><?= $totalPay['total_price'];?></span>
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="statistics-img right-img">
                            <img src="/imgs/site/data.png"/>
                        </div>
                        <div class="statistics-num right-num">
                            <p>总购买资源数</p>
                            <span><?= $totalPay['total_goods'];?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group field-order-confirm_at required">
                    <label class="col-lg-2 col-md-2 control-label" style="color: #999999; padding-top: 10px; text-align: left;">年度月支出金额:</label>
                    <div class="col-lg-4 col-md-4" style="padding-left: 0px;">
                        <?=    \kartik\widgets\DatePicker::widget([
                            'value'=>$dateRange,
           
                            'name' => 'dateRange',
                            //'presetDropdown' => true,
//                            'hideInput' => true,
//                            'convertFormat'=>true,
//                            'pluginOptions'=>[
//                                'locale'=>['format' => 'Y-m-d'],
//                                'allowClear' => true,
//                            ],
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
$js = <<<JS
        
JS;
?>