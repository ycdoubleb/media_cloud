<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\MediaAliyunAction;
use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaApprove;
use common\models\media\MediaRecycle;
use common\models\media\MediaType;
use common\models\media\searchs\MediaApproveSearch;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * ApproveController implements the CRUD actions for MediaApprove model.
 */
class ApproveController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有媒体申请数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaApproveSearch(['status' => MediaApprove::STATUS_APPROVEING]);
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['approves'],
                'key' => 'id',
                'pagination' => [
                    'defaultPageSize' => 10
                ]
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
        ]);
    }   

    /**
     * 申请入库。
     * 如果申请成功，浏览器将被重定向到“当前”页面。
     * @param string $media_id
     * @return mixed
     */
    public function actionAddApply($media_id)
    {
        // 所有媒体id
        $mediaIds = explode(',', $media_id);
        $post = Yii::$app->request->post();

        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaApprove::find()->where(['media_id' => $mediaIds])
                    ->andWhere(['or', ['result' => 1], ['status' => 0]])->asArray()->all();
                $result = ArrayHelper::index($result, 'media_id');
                // 申请说明
                $content = ArrayHelper::getValue($post, 'MediaApprove.content'); 
                // 过滤已存在的
                if(!in_array($media_id, array_keys($result))){
                    $model = new MediaApprove([
                        'media_id' => $media_id,'type' => MediaApprove::TYPE_INTODB_APPROVE,
                        'content' => $content, 'created_by' => Yii::$app->user->id
                    ]);
                    $model->save();
                }
                // 如果状态是【待审核】才更新
                if($result[$media_id]['status'] == MediaApprove::STATUS_APPROVEING){
                    $model = MediaApprove::findOne($result[$media_id]['id']);
                    $model->type = MediaApprove::TYPE_INTODB_APPROVE;
                    $model->content = $content;
                    $model->save();
                }

                $trans->commit();  //提交事务
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____apply', [
            'mediaIds' => json_encode($mediaIds)
        ]);
    }
    
    /**
     * 申请删除。
     * 如果申请成功，浏览器将被重定向到“当前”页面。
     * @param string $media_id
     * @return mixed
     */
    public function actionDelApply($media_id)
    {
        // 所有媒体id
        $mediaIds = explode(',', $media_id);
        $post = Yii::$app->request->post();

        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaApprove::find()->where(['media_id' => $mediaIds])
                    ->andWhere(['or', ['result' => 1], ['status' => 0]])->asArray()->all();
                $result = ArrayHelper::index($result, 'media_id');
                // 申请说明
                $content = ArrayHelper::getValue($post, 'MediaApprove.content'); 
                // 过滤已存在的
                if(!in_array($media_id, array_keys($result))){
                    $model = new MediaApprove([
                        'media_id' => $media_id,'type' => MediaApprove::TYPE_DELETE_APPROVE,
                        'content' => $content, 'created_by' => Yii::$app->user->id
                    ]);
                    $model->save();
                }
                // 如果状态是【待审核】才更新
                if($result[$media_id]['status'] == MediaApprove::STATUS_APPROVEING){
                    $model = MediaApprove::findOne($result[$media_id]['id']);
                    $model->type = MediaApprove::TYPE_DELETE_APPROVE;
                    $model->content = $content;
                    $model->save();
                }

                $trans->commit();  //提交事务
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____apply', [
            'mediaIds' => json_encode($mediaIds)
        ]);
    }

    /**
     * 通过申请
     * 如果成功，浏览器将被重定向到“当前”页面。
     * @param string $id
     * @return mixed
     */
    public function actionPassApprove($id)
    {
        // 所有id
        $ids = explode(',', $id);
        $post = Yii::$app->request->post();
        
        if(\Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaApprove::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
                $result = ArrayHelper::index($result, 'id');
                // 反馈信息
                $feedback = ArrayHelper::getValue($post, 'MediaApprove.feedback'); 
                // 过滤已经审批的
                if(!in_array($id, array_keys($result))){
                    $model = MediaApprove::findOne($id);
                    /* 需要保存的申请数据 */
                    $model->status = MediaApprove::STATUS_APPROVED;
                    $model->result = MediaApprove::RESULT_PASS_YES;
                    $model->feedback = $feedback;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();
                    if($model->save()){
                        $mediaModel = Media::findOne($model->media_id);
                        switch ($model->type){
                            case MediaApprove::TYPE_INTODB_APPROVE:
                                // 改变media状态需要满足审核类型是【入库申请】
                                if($mediaModel->mediaType->sign == MediaType::SIGN_VIDEO){
                                    // 如果视频转码需求是自动则转码
                                    if($mediaModel->detail->mts_need){
                                        MediaAliyunAction::addVideoTranscode($mediaModel->id);   // 转码
                                    }else{
                                        $mediaModel->status = Media::STATUS_PUBLISHED;
                                    }
                                }else{
                                    $mediaModel->status = Media::STATUS_PUBLISHED;
                                }
                                $mediaModel->save(true, ['status']);
                                break;
                            case MediaApprove::TYPE_DELETE_APPROVE:
                                // 媒体申请删除状态
                                $mediaModel->del_status = Media::DEL_STATUS_APPLY;
                                if($mediaModel->save(true, ['del_status'])){
                                    // 创建回收站数据需要满足审核类型是【删除申请】
                                    $recycleModel = new MediaRecycle(['media_id' => $model->media_id, 'created_by' => $model->handled_by]);
                                    $recycleModel->save();
                                }
                                break;
                        }
                    }
                }
                
                $trans->commit();  //提交事务
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____approve', [
            'ids' => json_encode($ids)
        ]);
    }
    
    /**
     * 不通过申请
     * 如果成功，浏览器将被重定向到“当前”页面。
     * @param string $id
     * @return mixed
     */
    public function actionNotApprove($id)
    {   
        // 所有id
        $ids = explode(',', $id);
        $post = Yii::$app->request->post();
        
        if(\Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaApprove::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
                $result = ArrayHelper::index($result, 'id');
                // 反馈信息
                $feedback = ArrayHelper::getValue($post, 'MediaApprove.feedback'); 
                // 过滤已经审批的
                if(!in_array($id, array_keys($result))){
                    $model = MediaApprove::findOne($id);
                    /* 需要保存的申请数据 */
                    $model->status = MediaApprove::STATUS_APPROVED;
                    $model->result = MediaApprove::RESULT_PASS_NO;
                    $model->feedback = $feedback;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();
                    $model->save();
                }

                $trans->commit();  //提交事务
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____approve', [
            'ids' => json_encode($ids)
        ]);
    }
}
