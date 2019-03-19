
<?php

use common\widgets\zTree\zTreeAsset;
use yii\web\View;

zTreeAsset::register($this);

?>

<div class="zTree-dropdown-container zTree-dropdown-container--krajee">
    <!-- 模拟select点击框 以及option的text值显示-->
    <span id="zTree-dropdown-name" class="zTree-dropdown-selection zTree-dropdown-selection--single" onclick="showTree()" ></span> 
    <!-- 模拟select右侧倒三角 -->
    <i class="zTree-dropdown-selection__arrow"></i>
    <!-- 存储 模拟select的value值 -->
    <input id="zTree-dropdown-value" type="hidden" name="orgCode" />
    <!-- zTree树状图 相对定位在其下方 -->
    <div class="zTree-dropdown-options ztree"  style="display:none;">
        <ul id="zTree-dropdown"></ul>
    </div>  
</div>

<script type="text/javascript">
   
    //树状图展示
    var treeDataList = <?= $dataProvider ?>;
   
    window.onload = function(){
        zTreeDropdown('zTree-dropdown', 'zTree-dropdown-name', 'zTree-dropdown-valu', treeDataList)
    }
  

 
</script> 

