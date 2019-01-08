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
            [['id', 'media_id'], 'integer'],
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
     * @param string $content  内容
     * @param string $wate_ids 水印id
     * @return ApiResponse
     */
    public static function savaMediaDetail($media_id, $content, $wate_ids = null)
    {
        $data = []; // 返回的数据
        /** 开启事务 */
        $trans = Yii::$app->db->beginTransaction();
        try
        {  
            $model = self::findOne(['media_id' => $media_id]);
            if($model == null){
                $model = new MediaDetail([
                    'media_id' => $media_id,
                    'content' => $content,
                    'mts_watermark_ids' => $wate_ids,
                ]);
            }else{
                $model->content = $content;
            }
                        
            if($model->save()){
                $trans->commit();  //提交事务
                $data = new ApiResponse(ApiResponse::CODE_COMMON_OK);
            }else{
                $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, null, $model->getErrorSummary(true));
            }
        }catch (Exception $ex) {
            $trans ->rollBack(); //回滚事务
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
        }
        
        return $data;
    }
    
}
