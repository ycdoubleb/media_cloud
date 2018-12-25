<?php

namespace common\models\media;

use Yii;

/**
 * This is the model class for table "{{%media_attribute}}".
 *
 * @property string $id
 * @property string $category_id    所属类目，关联media_category表id字段
 * @property string $name           属性名
 * @property int $index_type        检索方式 0不检查 1关键字检索 2范围检索
 * @property int $input_type        输入方式 1单选 2多选 3单行输入 4多行输入
 * @property int $sort_order        排序
 * @property int $is_del            是否删除
 * @property int $value_length      值长度
 */
class MediaAttribute extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_attribute}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id'], 'required'],
            [['category_id', 'index_type', 'input_type', 'sort_order', 'is_del', 'value_length'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'name' => Yii::t('app', 'Name'),
            'index_type' => Yii::t('app', 'Index Type'),
            'input_type' => Yii::t('app', 'Input Type'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_del' => Yii::t('app', 'Is Del'),
            'value_length' => Yii::t('app', 'Value Length'),
        ];
    }
}
