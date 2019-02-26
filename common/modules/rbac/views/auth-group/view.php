<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\common\modules\rbac\models\AuthGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/rbac', 'Auth Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-group-view">


    <div class="modal-dialog" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
    
                <?= DetailView::widget([
                    'model' => $model,
                    'template' => '<tr><th class="detail-th">{label}</th><td class="detail-td">{value}</td></tr>',
                    'attributes' => [
                        'id',
                        'name',
                        'app',
                        'sort_order',
                    ],
                ]) ?>
                
            </div>
            
            <div class="modal-footer">
                
                <?= Html::button(Yii::t('app', 'Close'), ['class' => 'btn btn-default btn-flat', 'data-dismiss' => 'modal', 'aria-label' => 'Close']) ?>
                
            </div>
                
       </div>
    </div>

</div>
