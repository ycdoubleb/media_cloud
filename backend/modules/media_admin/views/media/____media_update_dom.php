<!--素材修改-->
<p>
    <?php foreach ($dataProvider as $name => $data): ?>
    <span>【<?= $name ?>】发生了修改，修改后为：【新】<?= $data ?></span><br />
    <?php endforeach; ?>
</p>

