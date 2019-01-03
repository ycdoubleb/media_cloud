<?php

namespace common\models\media;

use Yii;
use yii\db\ActiveRecord;
use yii\redis\ActiveQuery;

/**
 * This is the model class for table "{{%media_attribute}}".
 *
 * @property string $id
 * @property string $category_id    所属类目，关联media_category表id字段
 * @property string $name           属性名
 * @property int $index_type        检索方式 0不检查 1关键字检索 2范围检索
 * @property int $input_type        输入方式 1单选 2多选 3单行输入 4多行输入
 * @property int $is_required       是否必选 0否 1是
 * @property int $sort_order        排序
 * @property int $is_del            是否删除
 * @property int $value_length      值长度
 * 
 * @property MediaCategory $category    获取媒体类目
 */
class MediaAttribute extends ActiveRecord
{
    /** 单选-输入类型 */
    const SINGLE_SELECT_INPUT_TYPE = 1;
    
    /** 多选-输入类型 */
    const MULTPLE_SELECT_INPUT_TYPE = 2;
    
    /** 单行-输入类型 */
    const SINGLE_LINE_INPUT_TYPE = 3;
    
    /** 多行-输入类型 */
    const MULTPLE_LINE_INPUT_TYPE = 4;
    
    /**
     * 输入类型
     * @var array 
     */
    public static $inputTypeMap = [
        self::SINGLE_SELECT_INPUT_TYPE => '单选框',
        self::MULTPLE_SELECT_INPUT_TYPE => '多选框',
        self::SINGLE_LINE_INPUT_TYPE => '单行输入',
        self::MULTPLE_LINE_INPUT_TYPE => '多行输入'
    ];

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
            [['category_id', 'index_type', 'input_type', 'sort_order', 'is_del', 'is_required', 'value_length'], 'integer'],
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
            'is_required' => Yii::t('app', 'Is Required'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'is_del' => Yii::t('app', 'Is Del'),
            'value_length' => Yii::t('app', 'Value Length'),
        ];
    }
    
    /**
     * @return ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(MediaCategory::class, ['id' => 'category_id']);
    }
    
    // 保存前
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            $attrModel = self::find()->orderBy(['sort_order' => SORT_DESC])->one();
            $this->sort_order = $attrModel == null ? 1 : $attrModel->sort_order + 1;
            return true;
        }
        return false;
    }
    
    /**
     * 获取媒体属性
     * @param string|array $media_id
     * @param string $category_id
     * @return array
     */
    public static function getMediaAttributeByCategoryId($media_id = null, $category_id = null)
    {
        $query = self::find()->from(['Attribute' => self::tableName()]);
        $query->select(['Attribute.*', "GROUP_CONCAT(AttributeValue.id, '_', AttributeValue.value) AS attr_value"]);
        // 必要条件
        $query->andFilterWhere([
            'Attribute.is_del' => 0,
            'AttributeValue.is_del' => 0,
        ]);
        // 按media_id条件查询
        $query->andFilterWhere(['media_id' => $media_id]);
        // 按category_id条件查询
        $query->andFilterWhere(['category_id' => $category_id]);
        // 关联属性值表
        $query->leftJoin(['AttributeValue' => MediaAttributeValue::tableName()], 'AttributeValue.attribute_id = Attribute.id');
        // 关联媒体属性值关联表
        $query->leftJoin(['AttrValueRef' => MediaAttValueRef::tableName()], 'AttrValueRef.attribute_id = Attribute.id');
        // 按category_id分组
        $query->groupBy(['category_id', 'AttributeValue.attribute_id']);
        // 按sort_order上升排序
        $query->orderBy('sort_order');
        
        return $query->asArray()->all();
    }
}
