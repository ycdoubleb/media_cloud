<?php

namespace common\models\media;

use common\models\api\ApiResponse;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;


/**
 * This is the model class for table "{{%acl}}".
 *
 * @property string $id
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
 */
class Acl extends ActiveRecord
{
    /** 状态-暂停 */
    const STATUS_SUSPEND = 0; 
    /** 状态-正常 */
    const STATUS_NORMAL = 1;
    
    /**
     * 状态
     * @var array 
     */
    public static $statusMap = [
        self::STATUS_SUSPEND => '暂停',
        self::STATUS_NORMAL => '正常'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
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
    public function rules()
    {
        return [
            [['order_id', 'media_id', 'level', 'user_id', 'status', 'visit_count', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['media_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['order_sn'], 'string', 'max' => 20],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
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
    public function getMedia()
    {
        return $this->hasOne(Media::class, ['id' => 'media_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
    
    /**
     * 保存访问路径
     * @param int $order_id
     * @return ApiResponse
     */
    public static function saveAcl($order_id)
    { 
        $data = []; // 返回的数据
        
        try
        {
            // 查询已经存在的acl
            $aclResults = self::find()->where(['order_id' => $order_id])->asArray()->all();
            if(count($aclResults) <= 0){
                // 查询商品数据
                $query = OrderGoods::find()->from(['Goods' => OrderGoods::tableName()]);

                // 所需字段
                $query ->select([
                    'Goods.goods_id AS media_id', 'Media.name', 'Goods.order_id', 'Goods.order_sn', 
                    'Goods.created_by as user_id', 'VideoUrl.level',
                    "IF(MediaType.sign = '".MediaType::SIGN_VIDEO."', VideoUrl.url, Media.url) as url",
                ]);        

                // 关联媒体表
                $query->leftJoin(['Media' => Media::tableName()], 'Media.id = Goods.goods_id');
                // 关联媒体类型表
                $query->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id');
                // 关联视频地址表
                $query->leftJoin(['VideoUrl' => VideoUrl::tableName()], 'VideoUrl.media_id = Media.id');

                // 条件查询
                $query->where(['Goods.order_id' => $order_id, 'Goods.is_del' => 0]);

                // 查询结果
                $goodsRows = $query->asArray()->all();

                // 合并创建时间和更新时间
                foreach ($goodsRows as &$item){
                    $item['created_at'] = time();
                    $item['updated_at'] = time();
                }
            
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), array_keys($goodsRows[0]), array_values($goodsRows))->execute();
                $data = new ApiResponse(ApiResponse::CODE_COMMON_OK, null, $goodsRows);
            }else{
                $data = new ApiResponse(ApiResponse::CODE_COMMON_OK, '数据已经存在。', $aclResults);
            }
            
        } catch (Exception $ex){
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
        }
        
        return $data;
    }
}
