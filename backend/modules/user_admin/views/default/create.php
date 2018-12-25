<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
$this->title = Yii::t('app', '{New}{User}', [
    'New' => Yii::t('app', 'New'), 'User' => Yii::t('app', 'User')
]);

?>

<h1>
    <?php echo $this->title ?>
</h1> 

<div class="create-user">
    
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel"><?= Html::encode($this->title) ?></h4>
            </div>
            
            <div class="modal-body">
                <?= $this->render('_form', ['model'=>$model] ) ?>
            </div>    
                
            <div class="modal-footer">
                <?= Html::button(Yii::t('app', 'Confirm'), [
                    'class'=>'btn btn-primary btn-flat',  'data-dismiss' => 'modal', 'aria-label' => 'Close'
                ]) ?>
            </div>
                
       </div>
    </div>

</div>

