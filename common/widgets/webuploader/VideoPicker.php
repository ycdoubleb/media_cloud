<?php

namespace common\widgets\webuploader;

/**
 * 视频获取器
 * 功能：上传视频，或者 多个视频
 *
 * @author Administrator
 */
class VideoPicker extends FilePicker {

    protected $extensions = 'mp4,flv,mkv,avi,wmv';
    protected $mimeTypes = 'video/*';

}
