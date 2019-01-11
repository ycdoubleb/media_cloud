<?php

namespace backend\modules\media_admin\controllers;

use common\components\aliyuncs\MediaAliyunAction;
use common\models\media\Media;
use common\models\media\MediaApprove;
use common\models\media\MediaRecycle;
use common\models\media\MediaType;
use common\models\media\searchs\MediaApproveSearch;
use Yii;
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
        $searchModel = new MediaApproveSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['approves'],
                'key' => 'id'
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
        ]);
    }   

    /**
     * 申请入库。
     * 如果申请成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionAddApply()
    {
        if(\Yii::$app->request->isPost){
            // 所有媒体id
            $mediaIds = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'media_id'));  
            // 申请说明
            $content = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.content'); 

            //查找已经存在的
            $result = MediaApprove::find()->from(['Approve' => MediaApprove::tableName()])
                ->select(['Approve.*', 'Media.status as media_status'])
                ->leftJoin(['Media' => Media::tableName()], 'Media.id = Approve.media_id')
                ->where(['Approve.media_id' => $mediaIds])
                ->orWhere(['or', ['Media.status' => Media::STATUS_INSERTED_DB], ['Media.status' => Media::STATUS_PUBLISHED]])
                ->andWhere(['or', ['Approve.result' => 1], ['Approve.status' => 0]])->asArray()->all();
            $result = ArrayHelper::index($result, 'media_id');

            foreach($mediaIds as $id){
                // 过滤已经存在或通过的申请
                if(!isset($result[$id])){
                    // 新建一个申请入库
                    $model = new MediaApprove(['media_id' => $id,'type' => MediaApprove::TYPE_INTODB_APPROVE,
                        'content' => $content, 'created_by' => Yii::$app->user->id]);
                    $model->save();
                }else if($result[$id]['status'] == MediaApprove::STATUS_WAIT_APPROVE){
                    $model = MediaApprove::findOne($result[$id]['id']);
                    $model->type = MediaApprove::TYPE_INTODB_APPROVE;
                    $model->content = $content;
                    $model->update();
                }
            }
            
            Yii::$app->getSession()->setFlash('success','申请成功！');
            
            return $this->redirect(['media/index']);
        }
        
        return $this->renderAjax('apply');
    }
    
    /**
     * 申请删除。
     * 如果申请成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionDelApply()
    {
        if(\Yii::$app->request->isPost){
            // 所有媒体id
            $mediaIds = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'media_id'));  
            // 申请说明
            $content = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.content'); 

            //查找已经存在的
            $result = MediaApprove::find()->where(['media_id' => $mediaIds])
                ->andWhere(['or', ['result' => 1], ['status' => 0]])->asArray()->all();
            $result = ArrayHelper::index($result, 'media_id');

            foreach($mediaIds as $id){
                // 过滤已经存在或通过的申请
                if(!isset($result[$id])){
                    // 新建一个删除申请
                    $model = new MediaApprove(['media_id' => $id,'type' => MediaApprove::TYPE_DELETE_APPROVE,
                        'content' => $content, 'created_by' => Yii::$app->user->id]);
                    $model->save();
                }else if($result[$id]['status'] == MediaApprove::STATUS_WAIT_APPROVE){
                    $model = MediaApprove::findOne($result[$id]['id']);
                    $model->type = MediaApprove::TYPE_DELETE_APPROVE;
                    $model->content = $content;
                    $model->update();
                }
            }
            
            Yii::$app->getSession()->setFlash('success','申请成功！');
            
            return $this->redirect(['media/index']);
        }
        
        return $this->renderAjax('apply');
    }

    /**
     * 通过申请
     * 如果成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionPassApprove()
    {
        if(\Yii::$app->request->isPost){
            // 所有id
            $ids = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'));  
            // 反馈信息
            $feedback = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.feedback'); 

            //查找已经存在的
            $result = MediaApprove::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
            $result = ArrayHelper::index($result, 'id');

            foreach($ids as $id){
                // 过滤已经审批的
                if(!isset($result[$id])){
                    $model = MediaApprove::findOne($id);
                    /* 需要保存的申请数据 */
                    $model->status = MediaApprove::STATUS_ALREADY_APPROVE;
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
                                    }
                                    $mediaModel->status = Media::STATUS_PUBLISHED;
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
            }
            
            Yii::$app->getSession()->setFlash('success','审批成功！');
            
            return $this->redirect(['index']);
        }
        
        return $this->renderAjax('approve');
    }
    
    /**
     * 不通过申请
     * 如果成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionNotApprove()
    {
        if(\Yii::$app->request->isPost){
            // 所有id
            $ids = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'));  
            // 反馈信息
            $feedback = ArrayHelper::getValue(Yii::$app->request->post(), 'MediaApprove.feedback'); 

            //查找已经存在的
            $result = MediaApprove::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
            $result = ArrayHelper::index($result, 'id');

            foreach($ids as $id){
                // 过滤已经审批的
                if(!isset($result[$id])){
                    $model = MediaApprove::findOne($id);
                    /* 需要保存的申请数据 */
                    $model->status = MediaApprove::STATUS_ALREADY_APPROVE;
                    $model->result = MediaApprove::RESULT_PASS_NO;
                    $model->feedback = $feedback;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();
                    $model->save();
                }
            }
                
            Yii::$app->getSession()->setFlash('success','审批成功！');
            
            return $this->redirect(['index']);
        }
        
        return $this->renderAjax('approve');
    }
}
