<?php

namespace common\widgets\webuploader;

/**
 * 图片获取器
 * 功能：上传头像，或者多长图片
 *
 * @author Administrator
 */
class ImagePicker extends FilePicker {

    protected $extensions = 'gif,jpg,jpeg,bmp,png';
    protected $mimeTypes = 'image/*';

}
