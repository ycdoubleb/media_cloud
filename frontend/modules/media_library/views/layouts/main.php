<?php

use frontend\modules\media_library\assets\MainAssets;
use yii\web\View;

/* @var $this View */
/* @var $content string */


MainAssets::register($this);

//$this->title = Yii::t('app', 'CourseFactory');

?>

<?php

$html = <<<Html
    <!-- 头部 -->
    <header class="header"></header>
    <!-- 内容 -->
    <div class="container content">
        
Html;

    $content = $html . $content . '</div>';
    echo $this->render('@app/views/layouts/main',['content' => $content]); 
?>
<script>
   
</script>
