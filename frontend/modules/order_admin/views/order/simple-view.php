<?php

use frontend\modules\media_library\assets\MainAssets;
use frontend\modules\order_admin\assets\ModuleAssets;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* 订单核查页 */

$this->title = Yii::t('app', 'Order Verification');

MainAssets::register($this);
ModuleAssets::register($this);

$tabs = ArrayHelper::getValue($filters, 'tabs', 'order_info');  //显示内容

?>

<div class="order_admin mediacloud">
    <!--头部信息-->
    <div class="header order-checking">
        <div class="container">
            <div class="pull-left">
                <img src="/imgs/site/checking.png">
                <span>订单核查</span>
            </div>
            <div class="pull-right">
                <img src="/imgs/site/pay.png">
            </div>
        </div>
    </div>
    
    <!--内容显示-->
    <div class="cart-view">
        <!--订单信息-->
        <div class="container content">
            <div class="checking-order common">
                <div class="mc-tabs">
                    <ul class="list-unstyled">
                        <li id="order_info">
                            <?= Html::a('订单信息', array_merge(['simple-view'], array_merge($filters, ['tabs' => 'order_info'])), ['title' => '订单信息']);?>
                        </li>
                        <li id="order_media">
                            <?= Html::a('媒体列表', array_merge(['simple-view'], array_merge($filters, ['tabs' => 'order_media'])), ['title' => '媒体列表']);?>
                        </li>
                    </ul>
                </div>
                <div class="mc-panel set-bottom">
                    <?php if ($tabs == 'order_info') {
                        // 订单信息
                        echo $this->render('____orderinfo',[
                            'model' => $model,
                        ]);
                    } else { 
                        // 订单商品
                        echo $this->render('____ordermedia',[
                            'dataProvider' => $dataProvider,
                        ]);
                    } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

$js = <<<JS
    //标签页选中效果
    $(".mc-tabs ul li[id=$tabs]").addClass('active');
        
JS;
    $this->registerJs($js,  View::POS_READY);
?>