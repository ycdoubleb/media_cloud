<?php

namespace common\models\media;

use common\models\api\ApiResponse;
use Yii;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 * This is the model class for table "{{%media_detail}}".
 *
 * @property string $id
 * @property string $media_id   媒体id，关联media表id字段
 * @property string $content    媒体描述/简介
 * @property string $mts_need   是否需要转码服务：0否 1是
 * @property string $mts_watermark_ids 转码水印id,多个使用,分隔，关联watermark表id字段
 */
class MediaDetail extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%media_detail}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'media_id', 'mts_need'], 'integer'],
            [['content'], 'string'],
            [['mts_watermark_ids'], 'string', 'max' => 255],
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
            'media_id' => Yii::t('app', 'Media ID'),
            'content' => Yii::t('app', 'Content'),
            'mts_watermark_ids' => Yii::t('app', 'Mts Watermark Ids'),
        ];
    }
    
    /**
     * 保存媒体详情
     * @param int $media_id  
     * @param array $columns ['content', 'mts_need', 'mts_watermark_ids']  
     * @return ApiResponse
     */
    public static function savaMediaDetail($media_id, $columns)
    {
        $data = []; // 返回的数据
        try
        {  
            $model = self::findOne(['media_id' => $media_id]);
            if($model == null){
                $columns = array_merge(['media_id' => $media_id], $columns);
                $query = \Yii::$app->db->createCommand()->insert(self::tableName(), $columns)->execute();
            }else{
                $query = \Yii::$app->db->createCommand()->update(self::tableName(), $columns, ['media_id' => $media_id])->execute();
            }
            
            $data = new ApiResponse(ApiResponse::CODE_COMMON_OK);
            
        }catch (Exception $ex) {
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
        }
        
        return $data;
    }
    
}
