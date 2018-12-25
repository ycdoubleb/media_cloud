<?php

namespace common\models\order;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%favorites}}".
 *
 * @property string $id
 * @property string $goods_id   商品（媒体）ID，关联media表id字段
 * @property string $group_name 分组名称
 * @property int $is_del        是否已删除 0否 1是
 * @property string $created_by 创建人（收藏人）
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Favorites extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%favorites}}';
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
            [['id', 'goods_id', 'created_by'], 'required'],
            [['id', 'goods_id', 'is_del', 'created_by', 'created_at', 'updated_at'], 'integer'],
            [['group_name'], 'string', 'max' => 50],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'goods_id' => Yii::t('app', 'Goods ID'),
            'group_name' => Yii::t('app', 'Group Name'),
            'is_del' => Yii::t('app', 'Is Del'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
