<?php

namespace common\models\media;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%acl}}".
 *
 * @property string $id
 * @property string $name       访问名称（媒体名称Or媒体名称_格式）
 * @property string $order_id   订单ID，关联order表id字段
 * @property string $order_sn   订单编号，关联order表order_sn字段
 * @property string $media_id   媒体ID，关联media表id字段
 * @property string $user_id    使用人ID，关联user表id字段
 * @property int $status        状态 0暂停 1正常
 * @property string $visit_count 访问次数
 * @property string $expire_at  到期时间
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Acl extends ActiveRecord
{
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
            [['order_id', 'media_id', 'user_id', 'status', 'visit_count', 'expire_at', 'created_at', 'updated_at'], 'integer'],
            [['media_id'], 'required'],
            [['name'], 'string', 'max' => 100],
            [['order_sn'], 'string', 'max' => 20],
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
            'user_id' => Yii::t('app', 'User ID'),
            'status' => Yii::t('app', 'Status'),
            'visit_count' => Yii::t('app', 'Visit Count'),
            'expire_at' => Yii::t('app', 'Expire At'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
