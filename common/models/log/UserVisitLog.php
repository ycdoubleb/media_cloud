<?php

namespace common\models\log;

use common\components\redis\RedisService;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_visit_log}}".
 *
 * @property string $id
 * @property string $order_id 订单ID，关联order,id
 * @property string $visit_time 访问时间
 * @property string $visit_count 访问次数
 */
class UserVisitLog extends ActiveRecord {

    /**
     * redis 键名前缀
     * format：
     * user_visit_log:{日期}{时段}:{订单ID}: => [order_id,user_id,visit_time,visit_count]
     * eg：user_visit_log:2019012401:1 => [order_id:1,user_id:1,visit_time:1572233424,visit_count:12]
     */
    const READIS_KEY = 'user_visit_log:';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%user_visit_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['user_id', 'order_id'], 'required'],
            [['user_id', 'visit_time', 'visit_count'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'visit_time' => Yii::t('app', 'Visit Time'),
            'visit_count' => Yii::t('app', 'Visit Count'),
        ];
    }

    /**
     * 增加访问量
     * 
     * @param int $user_id     用户ID
     */
    public static function visitIncrby($order_id, $user_id) {
        //key 
        //format = user_visit_log:2019012401:1 => [order_id:1,user_id:1,visit_time:1572233424,visit_count:12]
        //
        $time = time();
        $date = date("YmdH", $time);
        $key = self::READIS_KEY . "$date:$order_id";

        if (!RedisService::getRedis()->exists($key)) {
            //不存在，添加一条记录
            RedisService::getRedis()->hmset($key, ['order_id' => $order_id, 'user_id' => $user_id, 'visit_time' => $time, 'visit_count' => 0]);
        } else {
            //增加访问量
            RedisService::getRedis()->hincrby($key, 'visit_count', 1);
        }
    }

    /**
     * 同步缓存日志
     */
    public static function syncLogFromCache() {
        //获取缓存键
        $keys = RedisService::getRedis()->keys(self::READIS_KEY . "*");
        //需要更新的值
        $rows = [];
        //返回所有修改的acl
        foreach ($keys as $key) {
            $arr = RedisService::getRedis()->hmget($key, ['order_id', 'user_id', 'visit_time', 'visit_count']);
            $rows [] = array_values($arr);
        }
        if (count($rows) > 0) {
            //执行插入数据
            Yii::$app->db->createCommand()->batchInsert(self::tableName(), ['order_id', 'user_id', 'visit_time', 'visit_count'], $rows)->execute();
            //删除修改记录
            RedisService::getRedis()->del(...$keys);
        }
    }

}
