<?php

namespace common\models\order;

use common\models\media\Media;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_goods}}".
 *
 * @property string $id
 * @property string $order_id   订单ID，关联order表id字段
 * @property string $order_sn   订单编号
 * @property string $goods_id   商品（媒体）ID，关联media表id字段
 * @property string $num    购买数
 * @property string $price  商品价格（媒体单价）
 * @property string $amount
 * @property int $is_del    是否已删除 0否 1是
 * @property string $created_by 创建人（购买人），关联user表id字段
 * @property string $created_at 创建时间（购买时间）
 * @property string $updated_at 更新时间
 * 
 * @property Media $media
 * @property Order $order
 * @property User $createdBy
 */
class OrderGoods extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_goods}}';
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
            [['order_id', 'goods_id', 'created_by'], 'required'],
            [['order_id', 'goods_id', 'num', 'is_del', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['price', 'amount'], 'number'],
            [['order_sn'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'order_sn' => Yii::t('app', 'Order Sn'),
            'goods_id' => Yii::t('app', 'Goods ID'),
            'num' => Yii::t('app', 'Num'),
            'price' => Yii::t('app', 'Price'),
            'amount' => Yii::t('app', 'Amount'),
            'is_del' => Yii::t('app', 'Is Del'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMedia()
    {
        return $this->hasOne(Media::class, ['id' => 'goods_id']);
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
