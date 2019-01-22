<?php

use common\components\aliyuncs\Aliyun;
use yii\helpers\Html;

?>

<div class="alert alert-danger alert-dismissible" style="margin-bottom: 0px" role="alert">
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <div class="title">外链媒体上传流程：</div>
    <p>1、从eefile导出excel文件 <?= Html::a('（eefile平台）', 'http://eefile.gzedu.com/', ['class' => 'alert-link', 'target' => '_black']) ?> </p>
    <p>2、填写上传信息表<?= Html::a('（表格模板下载）', Aliyun::absolutePath('mediacloud/static/doc/template/import_link_medias_tamplate.xlsx?rand='. rand(0, 9999)), ['class' => 'alert-link']) ?></p>
    <p>3、导入上传信息表</p>
    <p>4、配置媒体属性</p>
    <p>5、提交</p>
</div>