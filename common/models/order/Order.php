<?php

namespace common\models\order;

use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $id
 * @property string $order_sn   订单编号，eg：201812131415221234
 * @property string $order_name 订单名称（媒体使用目白或者课程名称）
 * @property string $goods_num  商品总数（购买媒体总数）
 * @property string $goods_amount 商品总价（购买媒体总价）
 * @property string $order_amount 应付金额（商品总价-折扣）
 * @property string $user_note  用户留言
 * @property int $order_status  状态 0待付款 5待审核 6审核失败 10待确认 11已确认 15已取消 99已作废
 * @property int $play_status   付款状态，0未付款 1已付款
 * @property string $play_code  付款方式标识，如：alplay
 * @property string $play_sn    付款流水号
 * @property int $play_at       付款时间
 * @property int $confirm_at    确认时间（确认开通时间）
 * @property string $created_by 创建人id（购买人ID），关联user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 * 
 * @property PlayApprove[] $playApproves    
 * @property User $createdBy    关联用户表
 */
class Order extends ActiveRecord
{
    //待付款
    const ORDER_STATUS_READING_PAYING = 0;
    //待审核
    const ORDER_STATUS_TO_BE_AUDITED = 5;
    //审核失败
    const ORDER_STATUS_AUDIT_FAILURE = 6;
    //待确认
    const ORDER_STATUS_TO_BE_CONFIRMED = 10;
    //已确认
    const ORDER_STATUS_CONFIRMED = 11;
    //已取消
    const ORDER_STATUS_CANCELLED = 15;
    //已作废
    const ORDER_STATUS_INVALID = 99;
    
    //未付款
    const PLAY_STATUS_UNPAID = 0;
    //已付款
    const PLAY_STATUS_PAID = 1;

    /**
     * 订单状态名
     * @var array 
     */
    public static $orderStatusName = [
        self::ORDER_STATUS_READING_PAYING => '待付款',
        self::ORDER_STATUS_TO_BE_AUDITED => '待审核',
        self::ORDER_STATUS_AUDIT_FAILURE => '审核失败',
        self::ORDER_STATUS_TO_BE_CONFIRMED => '待确认',
        self::ORDER_STATUS_CONFIRMED => '已确认',
        self::ORDER_STATUS_CANCELLED => '已取消',
        self::ORDER_STATUS_INVALID => '已作废',
    ];
    
    /**
     * 支付状态名
     */
    public static $playStatusName = [
        self::PLAY_STATUS_UNPAID => '未付款',
        self::PLAY_STATUS_PAID => '已付款',
    ];
    
    public static $playCodeMode = [
       'eeplay' => '线下支付'
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%order}}';
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
            [['order_sn'], 'required'],
            [['goods_num', 'order_status', 'play_status', 'play_at', 'confirm_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['goods_amount', 'order_amount'], 'number'],
            [['order_sn', 'play_code'], 'string', 'max' => 20],
            [['order_name'], 'string', 'max' => 100],
            [['user_note'], 'string', 'max' => 255],
            [['play_sn'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_sn' => Yii::t('app', 'Order Sn'),
            'order_name' => Yii::t('app', 'Order Name'),
            'goods_num' => Yii::t('app', 'Goods Num'),
            'goods_amount' => Yii::t('app', 'Goods Amount'),
            'order_amount' => Yii::t('app', 'Order Amount'),
            'user_note' => Yii::t('app', 'User Note'),
            'order_status' => Yii::t('app', 'Order Status'),
            'play_status' => Yii::t('app', 'Play Status'),
            'play_code' => Yii::t('app', 'Play Code'),
            'play_sn' => Yii::t('app', 'Play Sn'),
            'play_at' => Yii::t('app', 'Play At'),
            'confirm_at' => Yii::t('app', 'Confirm At'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
    
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)) {
            // 设置订单号
            if($this->order_sn == null){
                $this->order_sn = date('YmdHis',time()) . rand(1000, 9999);
            }
            return true;
        }
        return false;
    }
    
    /**
     * @return ActiveQuery
     */
    public function getPlayApproves()
    {
        return $this->hasMany(PlayApprove::class, ['order_id' => 'id']);
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
