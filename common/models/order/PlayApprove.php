<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%play_approve}}".
 *
 * @property string $id
 * @property string $order_id   订单ID，关联order表id字段
 * @property string $certificate_url 凭证url，多个‘,’分隔
 * @property string $content    申请说明
 * @property int $status        状态 0待审核 1已审核
 * @property int $result        审核结果 0不通过 1通过
 * @property string $feedback   审核反馈
 * @property string $handled_by 审核人ID，关联admin_user表id
 * @property string $handled_at 审核时间
 * @property string $created_by 申请人ID，关联user表id字段
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class PlayApprove extends ActiveRecord
{
    //待审核
    const STATUS_TO_BE_AUDITED = 0;
    //已审核
    const STATUS_AUDITED = 1;

    //不通过
    const RESULT_NOT_PASS = 0;
    //通过
    const RESULT_PASS = 1;

    /**
     * 状态名
     * @var array 
     */
    public static $statusName = [
        self::STATUS_TO_BE_AUDITED => '待审核',
        self::STATUS_AUDITED => '已审核',
    ];
    
    /**
     * 结果名
     * @var array 
     */
    public static $resultName = [
        self::RESULT_NOT_PASS => '不通过',
        self::RESULT_PASS => '通过',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%play_approve}}';
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
            [['certificate_url'], 'required'],
            [['order_id', 'status', 'result', 'handled_by', 'handled_at', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['certificate_url', 'content', 'feedback'], 'string', 'max' => 255],
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
            'certificate_url' => Yii::t('app', 'Certificate Url'),
            'content' => Yii::t('app', 'Content'),
            'status' => Yii::t('app', 'Status'),
            'result' => Yii::t('app', 'Result'),
            'feedback' => Yii::t('app', 'Feedback'),
            'handled_by' => Yii::t('app', 'Handled By'),
            'handled_at' => Yii::t('app', 'Handled At'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
