<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\utils;

use Yii;

/**
 * Description of MIMEUtil
 * 文件后缀 MIME类型
 * @author Administrator
 */
class MIMEUtil {
    /**
     * 文件后缀 MIME类型
     * @var array 
     */
    public static $mime = [
        // 文档文件类型
        'doc' => 'application/msword',
        'dot' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentatio',
        
        // 文字文件类型
        'txt' => 'text/plain',
        'html' => 'text/html',
        'htm' => 'text/html',
        'xml' => 'text/xml',
        
        // 图片文件类型
        'bmp' => 'image/x-ms-bmp',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        
        // 音频文件类型
        'mp3' => 'audio/mpeg',
        'ogg' => 'audio/ogg',
        'mp4a' => 'audio/mp4',
        'wav' => 'audio/wav',
        'wma' => 'audio/x-ms-wma',
        
        // 视频文件类型
        'avi' => 'video/avi',
        'mp4' => 'video/mp4',
        'flv' => 'video/x-flv',
        'mkv' => 'video/x-matroska',
        'wmv' => 'video/x-ms-wmv',
        'swf' => 'application/x-shockwave-flash',
    ];
}
