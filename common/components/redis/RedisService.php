<?php

namespace common\components\redis;

use Yii;
use yii\redis\Connection;

class RedisService
{     
    /**
     * @desc 设置redis hash数据
     * @param string $redisKey
     * @param array $table
     * @param int $expire 过期时间单位秒
     * @return bool
     */
    public static  function setHash($redisKey = '', $table = [], $expire = 0 ){
        if(empty($redisKey) || empty($table)) return false;
        
        /* @var $rediis Connection */
        $rediis = Yii::$app->redis;
        
        $params = [$redisKey];
        foreach ($table as $key => $value){
            if(!empty($value)){
                if(is_bool($value)){
                    $value = (int)$value;
                }
                $params[] = $key;
                $params[] = $value;
            }
        }

        if(count($params) > 1) $rediis->executeCommand('HMSET', $params);
    }

    /**
     * @desc 读取redis Hash数据
     * @param string $redisKey
     * @param array $fields
     * @return array
     */
    public static function getHash($redisKey = '', $fields = []){
        $data = $rs =  [];
        
        /* @var $rediis Connection */
        $rediis = Yii::$app->redis;
        
        if(empty($fields)){
            $data = $rediis->HGetall(trim($redisKey));

            $count = count($data);
            for($i = 0 ; $i < $count ; $i++){
                if($i % 2 != 0){
                    $rs[$data[$i - 1]] = $data[$i];
                }
            }
        }else{
            $fieldCount = count($fields);
            for($i = 0 ; $i < $fieldCount ; $i++){
                $rs[$fields[$i]] = $rediis->HGet($redisKey, $fields[$i]);
            }
        }

        return $rs;
    }
    
    /**
     * 设置访问量
     * @param int|string $id
     * @param string $keyName   
     * @param int $increment
     */
    public static function incrementView($id, $keyName, $increment = 1){
        $id = intval($id);
        
        /* @var $rediis Connection */
        $rediis = Yii::$app->redis;
        
        if($id > 0 && $rediis->exists($keyName.$id)){
            $rediis->hincrby($keyName.$id, 'view', $increment);
        }

    }
    
    /**
     * 获取访问量
     * @param int|string $id
     * @param string $keyName   
     * @param int $increment
     */
    public static function getIncrement($id, $keyName){
        $id = intval($id);
        
        /* @var $rediis Connection */
        $rediis = Yii::$app->redis;
        
        if($id > 0 && $rediis->exists($keyName.$id)){
            $rediis->hlen($keyName.$id);
        }

    }
}