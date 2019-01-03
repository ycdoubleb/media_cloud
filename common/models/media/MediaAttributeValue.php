<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%media_attribute_value}}".
 *
 * @property string $id
 * @property string $attribute_id 属性id 关联media_attribute表id字段
 * @property string $value  ''
 * @property int $is_del    是否已删除 1是 0否
 * 
 * @property MediaAttribute $mediaAttribute     获取媒体属性
 */
class MediaAttributeValue extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_attribute_value}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['attribute_id'], 'required'],
            [['attribute_id', 'is_del'], 'integer'],
            [['value'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'attribute_id' => Yii::t('app', 'Attribute ID'),
            'value' => Yii::t('app', 'Value'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getMediaAttribute() {
        return $this->hasOne(MediaAttribute::class, ['id' => 'attribute_id']);
    }
}
