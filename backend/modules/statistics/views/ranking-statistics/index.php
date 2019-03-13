<?php

use backend\modules\statistics\assets\StatisticsModuleAsset;
use common\widgets\charts\ChartAsset;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */

StatisticsModuleAsset::register($this);
ChartAsset::register($this);

$this->title = Yii::t('app', 'Ranking Statistics');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="ranking-statistics-index">
    <div class="statistics-view">
        <div class="mc-title"><i class="glyphicon glyphicon-stats"></i>素材排行</div>
        <div class="statistics-form mc-form">
            <form id="order-form" class="form-horizontal">
                <!--时间段搜索-->
                <div class="form-group field-order-confirm_at required">
                    <label class="control-label">时间:</label>
                    <div class="control-input years-input">
                        <?= Select2::widget([
                            'name' => 'year',
                            'value' => $year,
                            'data' => $years,
                            'hideSearch' => true,
                            'pluginEvents' => ['change' => 'function(){ submitForm()}']
                        ]);?>
                    </div>
                    <div class="control-input months-input">
                        <?= Select2::widget([
                            'name' => 'month',
                            'value' => $month,
                            'data' => $months,
                            'hideSearch' => true,
                            'pluginEvents' => ['change' => 'function(){ submitForm()}']
                        ]);?>
                    </div>
                </div>
                <!--选项卡 显示条件-->
                <?= Html::hiddenInput('tabs', $tabs, '')?>
            </form>
        </div>
    </div>
    
    <div class="statistics-view">
        <div class="mc-tabs">
            <ul class="list-unstyled">
                <li id="operator">
                    <?= Html::a('运营人收入金额', array_merge(['index'], array_merge($filters, ['tabs' => 'operator']))) ?>
                </li>
                <li id="purchaser">
                    <?= Html::a('购买人支出金额', array_merge(['index'], array_merge($filters, ['tabs' => 'purchaser']))) ?>
                </li>
                <li id="income">
                    <?= Html::a('素材收入金额', array_merge(['index'], array_merge($filters, ['tabs' => 'income']))) ?>
                </li>
                <li id="click">
                    <?= Html::a('素材学习次数', array_merge(['index'], array_merge($filters, ['tabs' => 'click']))) ?>
                </li>
                <li id="quote">
                    <?= Html::a('素材引用次数', array_merge(['index'], array_merge($filters, ['tabs' => 'quote']))) ?>
                </li>
            </ul>
        </div>
        <div class="mc-panel">
            <div class="panel-left">
                <?php
                    switch ($tabs){
                        case 'operator': echo $this->render('____operatorlist', ['listsData' => $operator['listsData']]);  break;
                        case 'purchaser': echo $this->render('____purchaserlist', ['listsData' => $purchaser['listsData']]);  break;
                        case 'income': echo $this->render('____incomelist', ['listsData' => $income['listsData']]);  break;
                        case 'click': echo $this->render('____clicklist', ['listsData' => $click['listsData']]);  break;
                        case 'quote': echo $this->render('____quotelist', ['listsData' => $quote['listsData']]);  break;
                    }
                ?>
            </div>
            <div class="panel-right">
                <?php
                    switch ($tabs){
                        case 'operator': echo $this->render('____operatorchart', ['operator' => $operator]);  break;
                        case 'purchaser': echo $this->render('____purchaserchart', ['purchaser' => $purchaser]);  break;
                        case 'income': echo $this->render('____incomechart', ['income' => $income]);  break;
                        case 'click': echo $this->render('____clickchart', ['click' => $click]);  break;
                        case 'quote': echo $this->render('____quotechart', ['quote' => $quote]);  break;
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php

$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
        
    /**
     * 提交表单
     */
    window.submitForm = function(){
        $('#order-form').submit();
    }
JS;
$this->registerJs($js, View::POS_READY);
?>