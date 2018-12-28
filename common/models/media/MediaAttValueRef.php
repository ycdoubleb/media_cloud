<?php

namespace common\models\media;

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
     * @param array $attrValues
     * @return boolean
     */
    public static function saveMediaAttValueRef($media_id, $attrValues)
    {
        if(!is_array($attrValues)) return false;
        try {
            $mediaAttValue = [];
            foreach ($attrValues as $attr_id => $attr_value) {
                // 判断提交上来的属性是单选还是多选
                if(is_array($attr_value)){
                    foreach ($attr_value as $value) {
                        $mediaAttValue[] = [
                            'media_id' => $media_id,
                            'attribute_id' => $attr_id,
                            'attribute_value_id' => $value,
                        ];
                    }
                }else{
                    $mediaAttValue[] = [
                        'media_id' => $media_id,
                        'attribute_id' => $attr_id,
                        'attribute_value_id' => $attr_value,
                    ];
                }
            }
            
            $batchInsert = Yii::$app->db->createCommand()->batchInsert(self::tableName(),
                isset($mediaAttValue[0]) ? array_keys($mediaAttValue[0]) : [], $mediaAttValue)->execute();
            return true;
        } catch (Exception $exc) {
            Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
        }
                
        return false;
    }
}
