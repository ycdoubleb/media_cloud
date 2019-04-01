<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* 选择支付方式的模态框 */

$this->title = Yii::t('app', 'Payment Mode');

?>
<div class="payment-method main mc-modal">

    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            <div class="modal-body">
                <a href="/order_admin/cart/play-approve?id=<?=$id;?>" class="pay">
                    <img src="/imgs/site/pay.png"/>
                    <div>线下支付</div>
                </a>
                <a href="javascript:;" class="alipay">
                    <img src="/imgs/site/alipay.png"/>
                    <div>支付宝</div>
                </a>
                <a href="javascript:;" class="wechatpay">
                    <img src="/imgs/site/wechatpay.png"/>
                    <div>微信支付</div>
                </a>
            </div>
        </div>
    </div>
</div>