<?php

use backend\modules\statistics\assets\StatisticsModuleAsset;
use common\widgets\charts\ChartAsset;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */

StatisticsModuleAsset::register($this);
ChartAsset::register($this);

$this->title = Yii::t('app', 'Single Statistics');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="single-statistics-index">
    <div class="statistics-view">
        <div class="mc-tabs">
            <ul class="list-unstyled">
                <li id="operator">
                    <?= Html::a('运营人', array_merge(['index'], ['tabs' => 'operator'])) ?>
                </li>
                <li id="purchaser">
                    <?= Html::a('购买人', array_merge(['index'], ['tabs' => 'purchaser'])) ?>
                </li>
                <li id="media">
                    <?= Html::a('素材', array_merge(['index'], ['tabs' => 'media'])) ?>
                </li>
            </ul>
        </div>
        <div class="mc-panel">
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
                    <!--特定搜索-->
                    <div class="form-group field-media required">
                        <?php if($tabs != 'media'):?>
                            <!--姓名搜索-->
                            <label class="control-label">姓名:</label>
                            <div class="control-input select-nickname">
                                <?= Select2::widget([
                                    'name' => 'nickname',
                                    'data' => $nicknameData,
                                    'value' => $userId,
                                    'options' => [
                                        'placeholder' => '请选择...',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true
                                    ],
                                ])?>
                            </div>
                        <?php else:?>
                            <!--素材编号搜索-->
                            <label class="control-label">编号:</label>
                            <div class="control-input" style="vertical-align: bottom">
                                <?= Html::input('text', 'media_id', $mediaId, [
                                    'class' => 'form-control mediaid-value'
                                ])?>
                            </div>
                        <?php endif;?>
                        <?= Html::a('统计', 'javascript:;', ['id' => 'submit', 'class' => 'btn btn-primary btn-flat', 
                            'style' => 'margin-left:15px; vertical-align:bottom'])?>
                    </div>
                    
                    <!--选项卡 显示条件-->
                    <?= Html::hiddenInput('tabs', $tabs, '')?>
                </form>
            </div>
            
            <div class="panel-content">
                <?php
                    switch ($tabs){
                        case 'operator': echo $this->render('____operator', ['operator' => $operator]);  break;
                        case 'purchaser': echo $this->render('____purchaser', ['purchaser' => $purchaser]);  break;
                        case 'media': echo $this->render('____media', ['media' => $media]);  break;
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
        
    //提交表单 
    $("#submit").click(function(){
        $('#order-form').submit();
    })
        
    /**
     * 提交表单
     */
    window.submitForm = function(){
        $('#order-form').submit();
    }
JS;
$this->registerJs($js, View::POS_READY);
?>