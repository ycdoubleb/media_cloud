<?php

namespace common\models\media;

use common\components\redis\RedisService;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use common\utils\MysqlUtil;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "{{%acl}}".
 *
 * @property string $id
 * @property string $sn         编号
 * @property string $name       访问名称（媒体名称Or媒体名称_格式）
 * @property string $order_id   订单ID，关联order表id字段
 * @property string $order_sn   订单编号，关联order表order_sn字段
 * @property string $media_id   媒体ID，关联media表id字段
 * @property string $level      视频质量：0原始 1流畅 2标清 3高清 4超清
 * @property string $url        路径
 * @property string $user_id    使用人ID，关联user表id字段
 * @property int $status        状态 0暂停 1正常
 * @property string $visit_count 访问次数
 * @property string $expire_at  到期时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property Media $media
 * @property Order $order
 * @property User $user 
 * @property AclAction[] $aclAction
 */
class Acl extends ActiveRecord {

    /** 状态-暂停 */
    const STATUS_SUSPEND = 0;

    /** 状态-正常 */
    const STATUS_NORMAL = 1;

    /** 视频质量-原始 */
    const LEVEL_ORIGINAL = 0;

    /** 视频质量-流畅 */
    const LEVEL_LD = 1;

    /** 视频质量-标清 */
    const LEVEL_SD = 2;

    /** 视频质量-高清 */
    const LEVEL_HD = 3;

    /** 视频质量-超清 */
    const LEVEL_FD = 4;

    /**
     * acl数据缓存键值
     * 快速读取url路径等其它字段数据
     * 
     * RedisHash
     * mc_acl:data:sn => [id,url,media_id,user_id,visit_count]
     * @see redis
     */
    const REDIS_DATA_KEY = 'acl:data:';

    /**
     * 数据已修改集合
     * RedisSet
     */
    const REDIS_DIRTY_KEY = 'acl:dirty';

    /**
     * 临时访问sn
     * RedisString mid => sn
     */
    const REDIS_TEMP_SN_KEY = 'acl:temp_sn:';

    /**
     * 临时访问data
     * RedisString  sn => url
     */
    const REDIS_TEMP_DATA_KEY = 'acl:temp_data:';

    /**
     * 临时sn超时时间
     */
    const REDIS_TEMP_SN_EXPIRE_TIME = 24 * 60 *60;

    /**
     * 状态
     * @var array 
     */
    public static $statusMap = [
        self::STATUS_SUSPEND => '暂停',
        self::STATUS_NORMAL => '正常'
    ];

    /**
     * 视频质量
     * @var array 
     */
    public static $levelMap = [
        self::LEVEL_ORIGINAL => '原始',
        self::LEVEL_LD => '流畅',
        self::LEVEL_SD => '标清',
        self::LEVEL_HD => '高清',
        self::LEVEL_FD => '超清',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return '{{%acl}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors() {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['order_id', 'media_id', 'level', 'user_id', 'status', 'visit_count', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['media_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['sn', 'order_sn'], 'string', 'max' => 20],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'sn' => Yii::t('app', 'Sn'),
            'name' => Yii::t('app', 'Name'),
            'order_id' => Yii::t('app', 'Order ID'),
            'order_sn' => Yii::t('app', 'Order Sn'),
            'media_id' => Yii::t('app', 'Media ID'),
            'level' => Yii::t('app', 'Level'),
            'url' => Yii::t('app', 'Url'),
            'user_id' => Yii::t('app', 'User ID'),
            'status' => Yii::t('app', 'Status'),
            'visit_count' => Yii::t('app', 'Visit Count'),
            'expire_at' => Yii::t('app', 'Expire At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getMedia() {
        return $this->hasOne(Media::class, ['id' => 'media_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getOrder() {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAclAction() {
        return $this->hasMany(AclAction::className(), ['acl_id' => 'id'])
                        ->where(['acl_id' => $this->id]);
    }

    /**
     * 保存访问路径
     * @param int $order_id 订单ID
     * @throws Exception
     */
    public static function saveAcl($order_id) {
        try {
            // 查询已经存在的acl
            $aclResults = self::find()->where(['order_id' => $order_id])->asArray()->all();
            if (count($aclResults) <= 0) {
                // 查询商品数据
                $query = OrderGoods::find()->from(['Goods' => OrderGoods::tableName()]);

                // 所需字段
                $query->select([
                    'Goods.goods_id AS media_id', 'Media.name', 'Goods.order_id', 'Goods.order_sn',
                    'Goods.created_by as user_id', "IF(VideoUrl.level IS NUll, 0, VideoUrl.level) AS level",
                    "IF(VideoUrl.url IS NULL or VideoUrl.level = 0, Media.url, VideoUrl.url) as url"
                ]);

                // 关联媒体表
                $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Goods.goods_id');
                // 关联视频地址表
                $query->leftJoin(['VideoUrl' => VideoUrl::tableName()], '(VideoUrl.media_id = Media.id AND VideoUrl.is_del = 0)');

                // 条件查询
                $query->where(['Goods.order_id' => $order_id, 'Goods.is_del' => 0]);

                // 查询结果
                $goodsRows = $query->asArray()->all();

                // 合并sn、level、created_at、updated_at
                foreach ($goodsRows as &$item) {
                    $item['sn'] = RedisService::getRandomSN();
                    $item['created_at'] = time();
                    $item['updated_at'] = time();
                }

                Yii::$app->db->createCommand()->batchInsert(self::tableName(), array_keys($goodsRows[0]), array_values($goodsRows))->execute();
            } else {
                throw new Exception('数据已经存在');
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /**
     * 更新访问列表路径
     * @param type $media_id    媒体ID
     * @param type $level       媒体等级
     */
    public static function updateAcl($media_id, $level = []) {
        $acls = self::find()->select([
                'Acl.id', 'Acl.sn', "IF(VideoUrl.url IS NULL or VideoUrl.level = 0, Media.url, VideoUrl.url) as url"
            ])->from(['Acl' => Acl::tableName()])
            ->leftJoin(['VideoUrl' => VideoUrl::tableName()], '(Acl.media_id = VideoUrl.media_id AND Acl.level = VideoUrl.level AND VideoUrl.is_del = 0)')
            ->leftJoin(['Media' => Media::tableName()], 'Media.id = Acl.media_id')
            ->where(['Media.id' => $media_id])
            ->andFilterWhere(['VideoUrl.level' => $level])
            ->asArray()->all();

        $acl_sn = [];
        foreach ($acls as &$value) {
            $acl_sn[] = $value['sn'];
            $value = [$value['id'], $value['url']];
        }

        if (count($acl_sn) > 0) {
            // 创建批量插入(数据重复时更新指定字段)
            $sql = MysqlUtil::createBatchInsertDuplicateUpdateSQL(self::tableName(), ['id', 'url'], $acls, ['url']);
            //执行更新
            Yii::$app->db->createCommand($sql)->execute();
            //清除访问列表缓存
            self::clearCache($acl_sn);
        }
    }

    /**
     * 清除一个或多个访问列表缓存
     * 
     * @param string|array $acl_sn      
     */
    public static function clearCache($acl_sn) {
        $acl_sns = is_array($acl_sn) ? $acl_sn : [$acl_sn];
        foreach ($acl_sns as &$acl_sn) {
            $acl_sn = self::REDIS_DATA_KEY . $acl_sn;
        }
        RedisService::getRedis()->del(...(array) $acl_sns);
    }

    /**
     * 增加访问量
     * 
     * @param int $acl_id     访问ID
     */
    public static function visitIncrby($acl_sn) {
        //添加增量
        $key = self::REDIS_DATA_KEY . $acl_sn;
        RedisService::getRedis()->hincrby($key, 'visit_count', 1);
        //设置已修改
        RedisService::getRedis()->sadd(self::REDIS_DIRTY_KEY, $key);
    }

    /**
     * 更新已修改的缓存访问数
     */
    public static function updateDirtyFromCache() {
        //返回所有修改的键
        $members = RedisService::getRedis()->smembers(self::REDIS_DIRTY_KEY);
        //需要更新的值
        $values = [];
        //返回所有修改的acl
        foreach ($members as $member) {
            $arr = RedisService::getRedis()->hmget($member, ['id', 'visit_count']);
            $values [] = array_values($arr);
        }

        $sql = MysqlUtil::createBatchInsertDuplicateUpdateSQL(self::tableName(), ['id', 'visit_count'], $values, ['visit_count']);
        //执行更新
        Yii::$app->db->createCommand($sql)->execute();
        //删除修改记录
        RedisService::getRedis()->del(self::REDIS_DIRTY_KEY);
    }

    /**
     * 按Sn获取ACL信息
     * @param int|string $sn    编码
     * @param array $fields     获取指定字段
     * @return array ['id', 'url', 'media_id', 'user_id', 'visit_count']
     */
    public static function getAclInfoBySn($sn, $fields = ['url']) {
        $key = self::REDIS_DATA_KEY . $sn;
        // 从缓存取出分类数据
        $acls = RedisService::getRedis()->hmget($key, $fields);

        // 没有缓存则从数据库获取数据
        if (empty(array_filter($acls))) {
            // 数据库读取数据
            $acls = self::findOne(['sn' => $sn, 'status' => 1]);
            if ($acls) {
                $acls = $acls->toArray(['id', 'url', 'media_id', 'order_id', 'user_id', 'visit_count']);
                // 设置缓存
                RedisService::getRedis()->hmset($key, $acls);
                if (!empty($fields)) {
                    $acls = ArrayHelper::filter($acls, $fields); //获取指定字段
                }
            }
        }

        return $acls;
    }

    /**
     * 获取临时访问码sn
     * 通过 getTempUrlBySn(sn) 返回媒体路径
     * 
     * @param int $media_id
     */
    public static function getTempSnByMid($media_id) {
        //查找临时访问 sn 码
        $key = self::REDIS_TEMP_SN_KEY . $media_id;
        if (RedisService::getRedis()->exists($key)) {
            //查找临时访问码
            return RedisService::getRedis()->get($key);
        } else {
            //生成临时访问码
            $sn = RedisService::getRandomSN();
            //保存临时访问码,设置1天内有效
            RedisService::getRedis()->setex($key, self::REDIS_TEMP_SN_EXPIRE_TIME, $sn);
            $media = Media::findOne(['id' => $media_id]);
            if ($media) {
                //缓存 临时码与媒体路径,设置一天有效
                RedisService::getRedis()->setex(self::REDIS_TEMP_DATA_KEY . $sn, self::REDIS_TEMP_SN_EXPIRE_TIME, $media->url);
                return $sn;
            } else {
                return "";
            }
        }
    }

    /**
     * 通过临时访问码返回媒体路径
     * 
     * @param string $sn
     */
    public static function getTempUrlBySn($sn) {
        $key = self::REDIS_TEMP_DATA_KEY . $sn;
        if (RedisService::getRedis()->exists($key)) {
            //查找临时访问路径
            return RedisService::getRedis()->get($key);
        } else {
            return "";
        }
    }

}
