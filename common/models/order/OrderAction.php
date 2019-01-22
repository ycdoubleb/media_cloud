<?php

namespace common\models\order;

use common\models\api\ApiResponse;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
 * 
 * @property Order $order
 * @property User $createdBy 
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
    
    /**
     * @return ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }
    
    /**
     * 保存订单操作日志
     * @param int $order_id     订单ID 
     * @param string $title     标题
     * @param string $content   内容（字符串）| 加载渲染的模板
     * @param int $order_status 订单状态
     * @param int $play_status  支付状态
     * @param int $created_by   操作人id（0为用户操作）,关联admin_user表id字段
     * @return ApiResponse
     */
    public static function savaOrderAction($order_id, $title, $content, $order_status, $play_status, $created_by = 0)
    {
        try
        {  
            $model = new OrderAction([
                'order_id' => $order_id,
                'title' => $title,
                'content' => $content,
                'order_status' => $order_status,
                'play_status' => $play_status,
                'created_by' => $created_by,
            ]);
             
            if(!$model->save()){
                throw new Exception('保存失败：' . $model->getErrorSummary(true));
            }
        }catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
}
