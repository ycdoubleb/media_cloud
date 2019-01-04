<?php

namespace backend\modules\media_admin\controllers;

use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaApprove;
use common\models\media\MediaRecycle;
use common\models\media\searchs\MediaApproveSearh;
use Yii;
use yii\db\Exception;
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
        $searchModel = new MediaApproveSearh();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }   

    /**
     * 创建 一个新的媒体申请。
     * 如果创建成功，返回保存的json信息。
     * @return mixed
     */
    public function actionCreate()
    {
        $get = Yii::$app->request->queryParams;
        $post = Yii::$app->request->post();
        $mediaIds = ArrayHelper::getValue($get, 'media_id');  // 所有媒体id
        $type = ArrayHelper::getValue($get, 'type');  // 申请类型
        $media_id = ArrayHelper::getValue($post, 'MediaApprove.media_id');  // 媒体id
        
        $model = MediaApprove::findOne(['media_id' => $media_id, 'status' => 0]);
        if($model == null){
            $model = new MediaApprove(['created_by' => Yii::$app->user->id]);
            $model->loadDefaultValues();
        }
        
        if ($model->load($post)) {
            \Yii::$app->response->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {  
                $is_submit = false;
                $data = []; // 返回的数据
                $model->type = $type;
                
                if($model->validate() && $model->save()){
                    $is_submit = true;
                }else{
                    $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, null, $model->getErrorSummary(true));
                }
                
                if($is_submit){
                    $trans->commit();  //提交事务
                    $data = new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $model->toArray());
                }
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
            
            return ['data' => $data];
        }

        return $this->renderAjax('create', [
            'model' => $model,
            'type' => $type,
            'media_ids' => json_encode(explode(',', $mediaIds))
        ]);
    }

    
    /**
     * 更新 现有的媒体申请
     * 如果更新成功，浏览器将被重定向到“视图”页面。
     * @return mixed
     */
    public function actionUpdate()
    {
        $get = Yii::$app->request->queryParams;
        $post = Yii::$app->request->post();
        $ids = ArrayHelper::getValue($get, 'id');  // 所有id
        $result = ArrayHelper::getValue($get, 'result');  // 结果
        $id = ArrayHelper::getValue($post, 'MediaApprove.id');  // id
        
        $model = MediaApprove::findOne($id);
        if($model == null){
            $model = new MediaApprove();
        }
        
        if ($model->load($post)) {
            \Yii::$app->response->format = 'json';
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {  
                $is_submit = false;
                $data = []; // 返回的数据
                // Media模型
                $mediaModel = Media::findOne($model->media_id);
                
                if($model->status == MediaApprove::STATUS_WAIT_APPROVE){
                    /* 需要保存的申请数据 */
                    $model->status = MediaApprove::STATUS_ALREADY_APPROVE;
                    $model->result = $result;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();

                    if($model->validate() && $model->save()){
                        $is_submit = true;
                        /* 若审核结果是【通过】 则执行 */
                        if($model->result == MediaApprove::RESULT_PASS_YES){
                            switch ($model->type){
                                case MediaApprove::TYPE_INTODB_APPROVE:
                                    // 改变media状态需要满足审核类型是【入库申请】
                                    $mediaModel->status = Media::STATUS_ALREADY_INTO_DB;
                                    $mediaModel->save(true, ['status']);
                                    break;
                                case MediaApprove::TYPE_DELETE_APPROVE:
                                    // 媒体申请删除状态
                                    $mediaModel->del_status = Media::DEL_STATUS_APPROVE;
                                    if($mediaModel->save(true, ['del_status'])){
                                        // 创建回收站数据需要满足审核类型是【删除申请】
                                        $recycleModel = new MediaRecycle(['media_id' => $model->media_id, 'created_by' => $model->handled_by]);
                                        $recycleModel->save();
                                    }
                                    break;
                            }
                        }
                    }else{
                        $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, null, $model->getErrorSummary(true));
                    }
                }else{
                    $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, '该审核已经处理过了，请勿重复', [
                        '媒体编码' => $model->media_id, '审核类型' => MediaApprove::$typeMap[$model->type]]);
                }
                    
                if($is_submit){
                    $trans->commit();  //提交事务
                    $data = new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $model->toArray());
                }
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
            
            return ['data' => $data];
        }
       
        return $this->renderAjax('update', [
            'model' => $model,
            'result' => $result,
            'ids' => json_encode(explode(',', $ids))
        ]);
    }
}
