<div class="zTree-dropdown-container zTree-dropdown-container--krajee">
    <!-- 模拟select点击框 以及option的text值显示-->
    <span id="<?= $id ?>" class="zTree-dropdown-selection zTree-dropdown-selection--single" >
        <span class="zTree-dropdown-selection__placeholder"><?= $placeholder ?></span>
    </span> 
    <!-- 模拟select右侧倒三角 -->
    <i class="zTree-dropdown-selection__arrow"></i>
    <!-- 存储 模拟select的value值 -->
    <input type="hidden" name="<?= $name ?>" />
    <!-- zTree树状图 相对定位在其下方 -->
    <div class="zTree-dropdown-options <?= $class ?>" style="display: none">
        <?php if(empty($data)): ?>
        <ul class="zTree-dropdown-results">
            <li class="zTree-dropdown-results__option">未找到结果</li>
        </ul>
        <?php else: ?>
        <ul id="<?= $plugin_container ?>"></ul>
        <?php endif; ?>
    </div>  
</div>