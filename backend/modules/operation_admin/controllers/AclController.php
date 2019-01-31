<?php

namespace backend\modules\operation_admin\controllers;

use backend\modules\operation_admin\searchs\AclSearch;
use common\models\api\ApiResponse;
use common\models\media\Acl;
use common\models\media\AclAction;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\redis\Connection;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AclController implements the CRUD actions for Acl model.
 */
class AclController extends Controller
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
     * 列出所有ACL数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AclSearch(['status' => Acl::STATUS_NORMAL]);
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['acls'],
                'key' => 'id'
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname')
        ]);
    }

    /**
     * 显示单个ACL模型。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        
        return $this->render('view', [
            'model' => $model,
            'actionDataProvider' => new ArrayDataProvider([
                'allModels' => $model->aclAction,
            ]),
        ]);
    }
    
    /**
     * 预览媒体
     * @param string $media_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionPreview($id)
    {        
        return $this->renderAjax('____preview', [
            'model' => $this->findModel($id),
        ]);
    }
    
    /**
     * 刷新缓存
     * 如果更新成功，浏览器将被重定向到“当前页”页面。
     * @param string $id
     * @return mixed
     */
    public function actionRefreshCach($id)
    {
        if(\Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            try
            {
                $model = $this->findModel($id);
                Acl::clearCache($model->sn);
                
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                return new ApiResponse(ApiResponse::CODE_COMMON_UNKNOWN, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____refresh_cach', [
            'ids' => json_encode(explode(',', $id)), // 所有id
        ]);
    }
    
    /**
     * 设置状态
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSetStatus()
    {
        if(\Yii::$app->request->isPost){
            // 所有id
            $ids = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'));  
            // 状态
            $status = ArrayHelper::getValue(Yii::$app->request->post(), 'Acl.status'); 
            // 原因
            $content = ArrayHelper::getValue(Yii::$app->request->post(), 'Acl.content'); 
            
            if(Acl::updateAll(['status' => $status], ['id' => $ids]) > 0){
                AclAction::savaAclAction($ids, Acl::$statusMap[$status], $content);
            }
                        
            Yii::$app->getSession()->setFlash('success','设置成功！');
            
            return $this->redirect(['index']);
        }
        
        return $this->renderAjax('____set_status');
    }
    
    /**
     * 查看 媒体操作
     * @param string $id
     * @return mixed
     */
    public function actionViewAction($id)
    {
       $model = AclAction::findOne($id);        
        
        return $this->renderAjax('____view_action', [
            'model' => $model,
        ]);
    }

    /**
     * 根据ACL的主键值查找ACL模型。
     * 如果找不到模型，将引发404 HTTP异常。
     * @param string $id
     * @return Acl the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Acl::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
