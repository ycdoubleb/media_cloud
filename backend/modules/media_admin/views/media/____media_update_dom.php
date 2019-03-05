<!--素材修改-->
<p>
    <?php foreach ($dataProvider as $name => $data): ?>
    <span>【<?= $name ?>】发生了修改，修改前为：【旧】<?= $data ?></span><br />
    <?php endforeach; ?>
</p>

