<?php

namespace common\models\log;

use common\components\redis\RedisService;
use common\utils\MysqlUtil;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%media_visit_log}}".
 *
 * @property string $id
 * @property string $media_id 媒体ID，关联media,id
 * @property string $visit_time 访问时间
 * @property string $visit_count 访问次数
 */
class MediaVisitLog extends ActiveRecord {

    /**
     * redis 键名前缀
     * format：
     * media_visit_log:{年月} => {member:media_id},{score:访问数量}
     * eg：media_visit_log:201901 => {member:201,score:241}
     */
    const READIS_KEY = 'media_visit_log:';

    /**
     * 数据已修改集合
     * RedisSet
     */
    const REDIS_DIRTY_KEY = 'media_visit_log:dirty';

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%media_visit_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['media_id'], 'required'],
            [['media_id', 'visit_time', 'visit_count'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'visit_time' => Yii::t('app', 'Visit Time'),
            'visit_count' => Yii::t('app', 'Visit Count'),
        ];
    }

    /**
     * 增加访问量
     * 
     * @param int $media_id     媒体ID
     */
    public static function visitIncrby($media_id) {
        $key = self::READIS_KEY . date("Ym", time());
        //设置media 访问量+1
        RedisService::getRedis()->zincrby($key, 1, $media_id);
        //设置已修改
        RedisService::getRedis()->sadd(self::REDIS_DIRTY_KEY, $media_id);
    }

    /**
     * 同步缓存日志
     */
    public static function syncLogFromCache() {
        $date = date("Ym", strtotime('yesterday'));
        $time = strtotime($date);

        $key = self::READIS_KEY . $date;
        //获取缓存键
        $members = RedisService::getRedis()->smembers(self::REDIS_DIRTY_KEY);
        //需要更新的值
        $rows = [];
        //返回所有修改的acl
        foreach ($members as $member) {
            $rows [] = [$member, RedisService::getRedis()->zscore($key, $member), $time];
        }
        //执行插入数据
        $sql = MysqlUtil::createBatchInsertDuplicateUpdateSQL(self::tableName(), ['media_id', 'visit_count', 'visit_time'], $rows, ['visit_count']);
        Yii::$app->db->createCommand($sql)->execute();
        //删除修改记录
        RedisService::getRedis()->del(self::REDIS_DIRTY_KEY);
    }

}
