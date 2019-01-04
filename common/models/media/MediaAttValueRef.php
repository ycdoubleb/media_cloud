<?php

namespace common\models\media;

use common\models\api\ApiResponse;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;

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
     * @param int $media_id
     * @param array $media_attrs    媒体属性
     * @return ApiResponse
     */
    public static function saveMediaAttValueRef($media_id, $media_attrs)
    {
        $data = []; // 返回的数据
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
                
                $data = new ApiResponse(ApiResponse::CODE_COMMON_OK);
            }else{
                $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, '媒体属性必须是为数组格式');
            }
        } catch (Exception $exc) {
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
        }
                
        return $data;
    }
    
    /**
     * 获取媒体属性值引用
     * @param int $media_id     媒体id
     * @return array
     */
    public static function getMediaAttValueRefByMediaId($media_id) 
    {
        $query = self::find()->from(['AttValueRef' => self::tableName()]);
        // 查询的字段
        $query->select(['Media.name AS media_name', 
            'Attribute.id AS attr_id', 'Attribute.name AS attr_name', 
            "GROUP_CONCAT(DISTINCT AttributeValue.`id` SEPARATOR '，') AS attr_value_id",
            "GROUP_CONCAT(DISTINCT AttributeValue.`value` SEPARATOR '，') AS attr_value",
        ]);
        // 必要条件
        $query->andFilterWhere([
            'Attribute.is_del' => 0,
            'AttributeValue.is_del' => 0,
            'AttValueRef.is_del' => 0,
        ]);
        // 按media_id查询条件
        $query->andFilterWhere(['media_id' => $media_id,]);
        // 关联媒体表
        $query->leftJoin(['Media' => Media::tableName()], 'Media.id = media_id');
        // 关联媒体属性表
        $query->leftJoin(['Attribute' => MediaAttribute::tableName()], 'Attribute.id = AttValueRef.attribute_id');
        // 关联媒体属性值表
        $query->leftJoin(['AttributeValue' => MediaAttributeValue::tableName()], 'AttributeValue.id = AttValueRef.attribute_value_id');
        // 按media_id、attribute_id分组
        $query->groupBy(['media_id', 'AttValueRef.attribute_id']);
        
        return $query->asArray()->all();
    }
}
