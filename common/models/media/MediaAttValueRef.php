<?php

namespace common\models\media;

use Yii;
use yii\base\Exception;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%media_att_value_ref}}".
 *
 * @property string $id
 * @property string $media_id       媒体id，关联media表id字段
 * @property string $attribute_id   属性id，关联media_attribute表id字段
 * @property string $attribute_value_id 属性值id，关联media_attribute_value表id字段
 * @property int $is_del    是否已删除 0否 1是
 */
class MediaAttValueRef extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_att_value_ref}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['media_id', 'attribute_id', 'attribute_value_id'], 'required'],
            [['media_id', 'attribute_id', 'attribute_value_id', 'is_del'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'media_id' => Yii::t('app', 'Media ID'),
            'attribute_id' => Yii::t('app', 'Attribute ID'),
            'attribute_value_id' => Yii::t('app', 'Attribute Value ID'),
            'is_del' => Yii::t('app', 'Is Del'),
        ];
    }
    
    /**
     * 保存媒体属性值关联关系
     * @param type $media_id
     * @param type $media_attrs     媒体属性值
     * @throws Exception
     */
    public static function saveMediaAttValueRef($media_id, $media_attrs)
    {
        try {
            if(is_array($media_attrs)){
                $mediaAttValue = [];
                foreach ($media_attrs as $attr_id => $attr_value) {
                    // 判断提交上来的属性是单选还是多选
                    if(is_array($attr_value)){
                        foreach ($attr_value as $value) {
                            $mediaAttValue[] = [$media_id, $attr_id, $value];
                        }
                    }else{
                        $mediaAttValue[] = [$media_id, $attr_id, $attr_value];
                    }
                    self::updateAll(['is_del' => 1], ['media_id' => $media_id, 'attribute_id' => $attr_id]);
                }
                //保存关联
                Yii::$app->db->createCommand()->batchInsert(self::tableName(),
                    ['media_id', 'attribute_id', 'attribute_value_id'], $mediaAttValue)->execute();
                
            }else{
                throw new Exception('媒体属性必须是为数组格式');
            }
            
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }
    }
    
    /**
     * 获取媒体属性值引用
     * @param int $media_id     媒体id
     * @param bool $key_to_value  返回键值对
     * @return array
     */
    public static function getMediaAttValueRefByMediaId($media_id, $key_to_value = true) 
    {
        $query = self::find()->from(['AttValueRef' => self::tableName()]);
        
        // 关联媒体属性表
        $query->leftJoin(['Attribute' => MediaAttribute::tableName()], 'Attribute.id = AttValueRef.attribute_id');
        // 关联媒体属性值表
        $query->leftJoin(['AttributeValue' => MediaAttributeValue::tableName()], 'AttributeValue.id = AttValueRef.attribute_value_id');
        
        // 查询的字段
        $query->select([ 
            'Attribute.id AS attr_id', 'Attribute.name AS attr_name', 
            "GROUP_CONCAT(DISTINCT AttributeValue.`id` SEPARATOR '，') AS attr_value_id",
            "GROUP_CONCAT(DISTINCT AttributeValue.`value` SEPARATOR '，') AS attr_value",
        ]);
        
        // 按条件查询
        $query->andFilterWhere([
            'AttValueRef.is_del' => 0, 'media_id' => $media_id
        ]);
        
        // 按media_id、attribute_id分组
        $query->groupBy(['media_id', 'AttValueRef.attribute_id']);
        // 按sort_order上升排序
        $query->orderBy('Attribute.sort_order');
        
        // 查询结果
        $results = $query->asArray()->all();
        
        if($key_to_value){
            return ArrayHelper::map($results, 'attr_id', 'attr_value_id');
        }
        
        return $results;
    }
}
