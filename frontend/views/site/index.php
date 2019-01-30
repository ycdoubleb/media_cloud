<?php

use common\widgets\charts\ChartAsset;
use frontend\assets\SiteAssets;
use yii\web\View;

/* @var $this View */

$this->title = '媒体云服务平台';

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
            <a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#carousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            
        </div>
        <!-- 弹出播放视频 -->
        <div class="video-box">
            <video class="container" controls="controls"></video>
            <img class="close-btn" src="/imgs/banner/close_icon.png"/>
        </div>
    </div>

    <!-- 内容 -->
    <div class="container">
        <div class="list">
            <!--媒体总量-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="statistics-img media-img">
                        <img src="/imgs/site/data.png"/>
                    </div>
                    <div class="media-num">
                        <p>媒体总数量</p>
                        <span><?= $totals['totalNumber'];?></span>
                    </div>
                </div>
                <div id="total" class="statistics-cart">
                    
                </div>
            </div>
            <!--本月媒体增量-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="statistics-img amount-img">
                        <img src="/imgs/site/data.png"/>
                    </div>
                    <div class="media-num">
                        <p>本月媒体增量</p>
                        <span><?= $month['totalNumber'];?></span>
                    </div>
                </div>
                <div id="month" class="statistics-cart">
                    
                </div>
            </div>
            <!--媒体总收入-->
            <div class="total-media media-show">
                <div class="top-info">
                    <div class="statistics-img visit-img">
                        <img src="/imgs/site/yuan.png"/>
                    </div>
                    <div class="media-num">
                        <p>媒体总收入</p>
                        <span><?= Yii::$app->formatter->asCurrency($amount['totalAmount']);?></span>
                    </div>
                </div>
                <div id="amount" class="statistics-cart">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$totals = json_encode($totals['statisticsByType']); //媒体总数
$month = json_encode($month['statisticsByType']);   //本月媒体增加量
$amount = json_encode($amount['statisticsByType']); //媒体总收入

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
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 个) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}个 ({d}%)'},
        document.getElementById('total'), $totals);
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 个) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}个 ({d}%)'},
        document.getElementById('month'), $month);
    new ccoacharts.PicChart({title:"",itemLabelFormatter:'{b} ( {c} 元) {d}%',tooltipFormatter:'{a} <br/>{b} : {c}元 ({d}%)'},
        document.getElementById('amount'), $amount);
JS;
$this->registerJs($js, View::POS_READY);
?> 