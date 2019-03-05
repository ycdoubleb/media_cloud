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
use yii\db\Query;
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
     * 列出所有素材申请数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaApproveSearch(['status' => MediaApprove::STATUS_APPROVEING]);
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'totalCount' => $results['total'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['approves'],
                'key' => 'id',
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
    public function actionAddApply($media_ids)
    {
        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            try
            {
                // 所有素材id
                $mediaIds = explode(',', $media_ids);
                // 申请说明
                $content = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.content'); 
                // 申请入库前先设置已经存在未处理的申请为作废
                MediaApprove::updateAll(['status' => MediaApprove::STATUS_CANCELED], ['media_id' => $mediaIds, 'status' => 0]);
                // 查询所有提交上来的素材
                $meidaResults = (new Query())->select(['id', 'name', 'status', 'tags'])
                    ->from(['Media' => Media::tableName()])->where(['id' => $mediaIds])->all();
                // 以素材id为索引
                $meidaResults = ArrayHelper::index($meidaResults, 'id');
                // 查找已经通过过的入库申请
                $approveResults = (new Query())->from(['MediaApprove' => MediaApprove::tableName()])
                    ->where(['media_id' => $mediaIds,'result' => 1,'type' => MediaApprove::TYPE_INTODB_APPROVE])
                    ->andWhere(['!=', 'status', MediaApprove::STATUS_CANCELED])->all();
                // 获取已经通过过的入库申请的素材id
                $approveResults = ArrayHelper::getColumn($approveResults, 'media_id');
                // 返回数据      
                $dataResults = [];
                foreach ($mediaIds as $id) {
                    if(isset($meidaResults[$id])){
                        // 【标签】数量至少是5个才可以创建入库申请
                        if(substr_count($meidaResults[$id]['tags'], ',') < 4){
                            $dataResults[] = [
                                'id' => $meidaResults[$id]['id'],
                                'name' => $meidaResults[$id]['name'],
                                'reason' => '标签数量不能少于5个'
                            ];
                            continue;
                        }
                        // 只有素材状态是为【待入库】才可以创建入库申请
                        if($meidaResults[$id]['status'] == Media::STATUS_INSERTING_DB){
                            // 过滤已存在的
                            if(!in_array($id, $approveResults)){
                                MediaApprove::savaMediaApprove($id, $content, MediaApprove::TYPE_INTODB_APPROVE);
                            }
                        }else{
                            $dataResults[] = [
                                'id' => $meidaResults[$id]['id'],
                                'name' => $meidaResults[$id]['name'],
                                'reason' => '素材已发布，无需重复申请入库'
                            ];
                        }
                    }
                }
                
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '操作成功', $dataResults);
                
            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____apply', [
            'media_ids' => $media_ids
        ]);
    }
    
    /**
     * 申请删除。
     * 如果申请成功，浏览器将被重定向到“当前”页面。
     * @param string $media_id
     * @return mixed
     */
    public function actionDelApply($media_ids)
    {
        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            try
            {
                // 所有素材id
                $mediaIds = explode(',', $media_ids);
                // 申请说明
                $content = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.content'); 
                // 申请删除前先设置已经存在未处理的申请为作废
                MediaApprove::updateAll(['status' => MediaApprove::STATUS_CANCELED], ['media_id' => $mediaIds, 'status' => 0]);
                // 查询所有提交上来的素材
                $meidaResults = (new Query())->select(['id', 'name', 'del_status'])
                    ->from(['Media' => Media::tableName()])->where(['id' => $mediaIds])->all();
                // 以素材id为索引
                $meidaResults = ArrayHelper::index($meidaResults, 'id');
                // 查找已经通过过的删除申请
                $approveResults = (new Query())->from(['MediaApprove' => MediaApprove::tableName()])
                    ->where(['media_id' => $mediaIds,'result' => 1,'type' => MediaApprove::TYPE_DELETE_APPROVE])
                    ->andWhere(['!=', 'status', MediaApprove::STATUS_CANCELED])->all();
                // 获取已经通过过的入库申请的素材id
                $approveResults = ArrayHelper::getColumn($approveResults, 'media_id');
                // 返回数据      
                $dataResults = [];
                foreach ($mediaIds as $id) {
                    if(isset($meidaResults[$id])){
                        // 只有素材删除状态是为【正常】才可以创建删除申请
                        if($meidaResults[$id]['del_status'] == 0){
                            // 过滤已存在的
                            if(!in_array($id, $approveResults)){
                                MediaApprove::savaMediaApprove($id, $content, MediaApprove::TYPE_DELETE_APPROVE);
                            }
                        }else{
                            $dataResults[] = [
                                'id' => $meidaResults[$id]['id'],
                                'name' => $meidaResults[$id]['name'],
                                'reason' => '素材已删除，无需重复申请删除'
                            ];
                        }
                    }
                }
              
                return new ApiResponse(ApiResponse::CODE_COMMON_OK, '操作成功', $dataResults);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
            
        return $this->renderAjax('____apply', [
            'media_ids' => $media_ids
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
                                        MediaAliyunAction::addVideoTranscode($mediaModel->id, false, '/media/tran-complete');   // 转码
                                        
                                    }else{
                                        $mediaModel->status = Media::STATUS_PUBLISHED;
                                    }
                                }else{
                                    $mediaModel->status = Media::STATUS_PUBLISHED;
                                }
                                $mediaModel->save(true, ['status']);
                                break;
                            case MediaApprove::TYPE_DELETE_APPROVE:
                                // 素材申请删除状态
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
                Yii::$app->getSession()->setFlash('success','操作成功！');
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
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
                Yii::$app->getSession()->setFlash('success','操作成功！');
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____approve', [
            'ids' => json_encode($ids)
        ]);
    }
}
