<?php

use common\widgets\charts\ChartAsset;
use frontend\assets\SiteAssets;
use yii\web\View;

/* @var $this View */

$this->title = Yii::$app->name;

SiteAssets::register($this);
ChartAsset::register($this);

?>

<div class="site-index">
    <!-- 宣传 -->
    <div class="banner">
        <div id="carousel" class="carousel slide">
            <?php if (count($banners) <= 0): ?>
            <div class="item">
                <img src="/imgs/banner/default.jpg">
            </div>
            <?php endif; ?>
            <div class="carousel-inner" role="listbox">
            <?php foreach ($banners as $index => $model): ?>
            <div class="item <?= $index == 0 ? 'active' : '' ?>">
                <div class="img-box" style="background:url('<?= $model->path ?>') no-repeat center top"></div>
                <?php if ($model->type == 2): ?>
                <!-- 如果是视频，即显示播放按钮 -->
                <div class="play-btn-box">
                    <img class="play-btn" src="/imgs/banner/play_icon.png" data-href="<?= $model->link ?>"/>
                </div>
                <?php endif; ?>
                <div class="carousel-caption" style="display:none;"></div>
            </div>
            <?php endforeach; ?>
            </div>
            <!-- 左右切换 -->
            <?php if (count($banners) > 1): ?>
            <a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            <?php endif; ?>
        </div>
        <!-- 弹出播放视频 -->
        <div class="video-box">
            <video class="container" controls="controls"></video>
            <img class="close-btn" src="/imgs/banner/close_icon.png"/>
        </div>
    </div>

    <!-- 内容 -->
    <div class="container">
        <!--material-->
        <div class="material-title">
            <p>MATERIAL</p>
            <div class="material">
                <span class="line"></span>
                <span class="txt">素材</span>
                <span class="line"></span>
            </div>
        </div>
        <!--素材数据-->
        <div class="list">
            <!--素材总量-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="media-num">
                        <div class="statistics-img media-img">
                            <img src="/imgs/site/data_blue.png"/>
                        </div><i class="fa fa-database"></i>
                        素材总数量 <span><?= $totals['totalNumber'];?></span> 个
                    </div>
                    <div class="media-details">
                        <?php foreach ($totals['statisticsByType'] as $key => $value): ?>
                            <div class="media-type">
                                <div class="mt-block block-<?= getMediaType($value['name']);?>"></div>
                                <?= $value['name']?>（<?= $value['value']?> 个）
                                    <?= $totals['totalNumber'] == 0 ? 0 
                                        : Yii::$app->formatter->asPercent($value['value']/$totals['totalNumber'], 2)?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <div id="total" class="statistics-cart">
                    
                </div>
            </div>
            <!--本月素材增量-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="media-num">
                        <div class="statistics-img amount-img">
                            <img src="/imgs/site/data_blue.png"/>
                        </div>
                        本月素材增量 <span><?= $month['totalNumber'];?></span> 个
                    </div>
                    <div class="media-details">
                        <?php foreach ($month['statisticsByType'] as $key => $value): ?>
                            <div class="media-type">
                                <div class="mt-block block-<?= getMediaType($value['name']);?>"></div>
                                <?= $value['name']?>（<?= $value['value']?> 个）
                                    <?= $month['totalNumber'] == 0 ? 0 
                                        :Yii::$app->formatter->asPercent($value['value']/$month['totalNumber'], 2)?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <div id="month" class="statistics-cart">
                    
                </div>
            </div>
            <!--素材总收入-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="media-num">
                        <div class="statistics-img visit-img">
                            <img src="/imgs/site/yuan_blue.png"/>
                        </div>
                        素材总收入 <span><?= Yii::$app->formatter->asCurrency($amount['totalAmount']);?></span>
                    </div>
                    <div class="media-details">
                        <?php foreach ($amount['statisticsByType'] as $key => $value): ?>
                            <div class="media-type">
                                <div class="mt-block block-<?= getMediaType($value['name']);?>"></div>
                                <?= $value['name']?>（<?= Yii::$app->formatter->asCurrency($value['value'])?>）
                                    <?= $amount['totalAmount'] == 0 ? 0 
                                        : Yii::$app->formatter->asPercent($value['value']/$amount['totalAmount'], 2)?>
                            </div>
                        <?php endforeach;?>
                    </div>
                </div>
                <div id="amount" class="statistics-cart">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php

//获取媒体类型
function getMediaType($data){
    switch ($data){
        case '视频' : $type = 'video';
            break;
        case '图片' : $type = 'image';
            break;
        case '文档' : $type = 'document';
            break;
        case '音频' : $type = 'audio';
            break;
        default : $type = 'default';
            break;
    }
    return $type;
}

$totals = json_encode($totals['statisticsByType']); //素材总数
$month = json_encode($month['statisticsByType']);   //本月素材增加量
$amount = json_encode($amount['statisticsByType']); //素材总收入
$optionColor = json_encode(['#fab142', '#00778d', '#96b800', '#01aba8']);   //自定义饼图颜色

$js = <<<JS
        
    //初始化轮播
    $('.carousel').carousel({
        interval: 3000
    });
    /* 播放按钮事件 */
    $('.carousel .play-btn').on('click',function(){
        playVideo($(this).attr('data-href'),true);
    });
    $('.carousel .play-btn').hover(
        function () {
            $('.carousel .active .img-box').addClass("blur");
        },
        function () {
            $('.carousel .active .img-box').removeClass("blur");
        }
    );
    /* 侦听视频播放完成事件 */
    $('.banner .video-box video').on('ended',function(){
        closeVideo();
    });
     
    /* 关闭视频按钮 */
    var close_btn_delay_id; 
    $('.banner .video-box .close-btn').on('click',function(){
        closeVideo();
    });
    /* 
     * 播放视频
     * @path {String} 视频路径
     * @autoplay {Boolean} 自动播放
     */
    function playVideo(path,autoplay){
        $('.carousel').carousel('pause');
        $('.banner .video-box video').hide();
        $('.banner .video-box').fadeIn(400,function(){
            $('.banner .video-box video').fadeIn(500);
            /* 鼠标移动显示关闭按钮 */
            $('.banner .video-box').mousemove(function(){
                clearTimeout(close_btn_delay_id);
                $('.banner .video-box .close-btn').fadeIn();
                close_btn_delay_id = setTimeout(function(){
                    $('.banner .video-box .close-btn').fadeOut();
                },2500);
            });;
        });
        $('.banner').css('height','562px');
        $('.banner .video-box video').attr('src', path);
        if(autoplay){
            $('.banner .video-box video').get(0).play();
        }
    }
    /* 退出视频播放 */
    function closeVideo(){
        clearTimeout(close_btn_delay_id);
        $('.banner .video-box video').get(0).pause();
        $('.banner .video-box').fadeOut(400);
        $('.banner').css('height','400px');
        $('.banner .video-box').off('mousemove');
        $('.banner .video-box .close-btn').fadeOut();
        $('.carousel').carousel('cycle')
    }

    // 统计结果
    new ccoacharts.PicChart({title:"",itemLabelShow:false, tooltipFormatter:'{a} <br/>{b} : {c}个 ({d}%)', optionColor:$optionColor},
        document.getElementById('total'), $totals);
    new ccoacharts.PicChart({title:"",itemLabelShow:false, tooltipFormatter:'{a} <br/>{b} : {c}个 ({d}%)', optionColor:$optionColor},
        document.getElementById('month'), $month);
    new ccoacharts.PicChart({title:"", itemLabelShow: false, tooltipFormatter: '{a} <br/>{b} : {c}元 ({d}%)', optionColor:$optionColor},
        document.getElementById('amount'), $amount);
JS;
$this->registerJs($js, View::POS_READY);
?> 