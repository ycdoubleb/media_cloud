<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%order_action}}".
 *
 * @property string $id
 * @property string $order_id   订单id，关联order表id字段
 * @property string $title      操作标题/类型
 * @property string $content    操作内容
 * @property int $order_status  订单状态
 * @property int $play_status   支付状态
 * @property string $created_by 操作人id（0为用户操作）,关联admin_user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class OrderAction extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order_action}}';
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
            [['order_id'], 'required'],
            [['order_id', 'order_status', 'play_status', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title'], 'string', 'max' => 20],
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
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'order_status' => Yii::t('app', 'Order Status'),
            'play_status' => Yii::t('app', 'Play Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
