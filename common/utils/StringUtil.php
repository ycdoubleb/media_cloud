<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\utils;

/**
 * Description of StringUtil
 *
 * @author Kiwi°
 */
class StringUtil {
    //put your code here
    /**
     * 补全文件路径
     * @param srting $path  路径
     * @param string|array $withStr 指定的字符，默认['http://', 'https://', '/']
     * @param srting $appendStr  补全的字符，默认‘/’
     * @return srting
     */
    public static function completeFilePath($path, $withStr = '', $appendStr = '/')
    {
        //如果$withStr为空的，默认['http://', 'https://', '/']
        if(empty($withStr)){
            $withStr = ['http://', 'https://', '/'];
        }
        
        //如果$withStr不是数组，默认转为数组
        if(!is_array($withStr)){
            $withStr = [$withStr];
        }
        //如果参数path为空，默认为空字符串
        if($path == null){
            $path = '';
        }
        //判断指定的字符串是否存在，若不存在则补全
        $isAppendStr = false;
        foreach ($withStr as $str) {
            if(stripos($path, "$str") !== 0){
                $isAppendStr = true;
            }else{
                $isAppendStr = false;
                break;
            }
        }
        return $isAppendStr ? $appendStr . $path : $path;
    }
    
    /**
     * 检查手机有效性，
     * 手机必须满足以下规定：
     * 1、必须为11位数字
     * 2、第一位数字必须为1
     * 3、第二位数字必须在345678中的其中一个
     * @param type $phone
     */
    public static function checkPhoneValid($phone) {
        return preg_match('/^[1][345678][0-9]{9}$/', $phone);
    }
    
    /**
     * 获取文件的后缀名
     * @param string $file
     * @return string
     */
    public static function getFileExtensionName($file) {
        $extName = '';
        //获取文件的后缀名
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        //判断文件后缀名的最后一个字符是否是大写
        if(preg_match('/^[A-Z]+$/', substr($ext, -1))){
            //判断后缀名最后一个字符是否为大写X
            if(substr($ext, -1) == 'X'){
                //在后缀名最后一个字符是大写X的情况下替换为小写
                $extName = str_replace(substr($extName, -1), 'x', $extName);
            }
        //判断后缀名最后一个字符是否为小写x
        }else if(substr($ext, -1) == 'x'){
            $extName = $ext;
        }else{
            $extName = $ext . 'x';
        }
        
        return $extName;
    }
    
    /**
     * 数字转为大写中文
     * @param integer $num
     * @return string
     */
    public static function toUpcaseChinese($num){
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        $num = round($num, 2);
        $num = $num * 100;
        if (strlen($num) > 11) {
            return "数据太长，没有这么大的钱吧，检查下";
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                $n = substr($num, strlen($num)-1, 1);
            } else {
                $n = $num % 10;
            }
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            $num = $num / 10;
            $num = (int)$num;
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            $m = substr($c, $j, 6);
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j-3;
                $slen = $slen-3;
            }
            $j = $j + 3;
        }
        if (substr($c, strlen($c)-3, 3) == '零') {
            $c = substr($c, 0, strlen($c)-3);
        }
        if (empty($c)) {
            return "零元整";
        }else{
            return $c . "整";
        }

    }
}
