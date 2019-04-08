<?php

namespace backend\modules\media_admin\controllers;

use common\models\api\ApiResponse;
use common\models\media\Media;
use common\models\media\MediaRecycle;
use common\models\media\searchs\MediaRecycleSearch;
use Yii;
use yii\base\Exception;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * RecycleController implements the CRUD actions for MediaRecycle model.
 */
class RecycleController extends Controller
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
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有回收站数据。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaRecycleSearch(['status' => MediaRecycle::STATUS_UNTREATED]);
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤
            'totalCount' => $results['total'],     //计算总数量
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['recycles'],
                'key' => 'id',
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
        ]);
    }

    /**
     * 还原 素材数据
     * 如果还原成功，浏览器将被重定向到“当前”页面。
     * @param string $id
     * @return mixed
     */
    public function actionRecovery($id)
    {
        // 所有id
        $ids = explode(',', $id);
        
        if(\Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaRecycle::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
                $result = ArrayHelper::index($result, 'id');
                // 过滤已经处理的
                if(!in_array($id, array_keys($result))){
                    $model = MediaRecycle::findOne($id);
                    /* 需要保存的回收站数据 */
                    $model->result = MediaRecycle::RESULT_RECOVERED;
                    $model->status = MediaRecycle::STATUS_HANDLED;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();
                    if($model->save()){
                        $mediaModel = Media::findOne($model->media_id);
                        $mediaModel->del_status = 0;
                        $mediaModel->save(true, ['del_status']);
                    }
                }
                
                $trans->commit();  //提交事务
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Operation Succeeded'));
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Operation Failed:') . $ex->getMessage());
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____handle_result', [
            'ids' => json_encode($ids)
        ]);
    }

    /**
     * 删除 素材数据
     * 如果还原成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // 所有id
        $ids = explode(',', $id);
        
        if(\Yii::$app->request->isPost){
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                //查找已经存在的
                $result = MediaRecycle::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
                $result = ArrayHelper::index($result, 'id');
                // 过滤已经处理的
                if(!in_array($id, array_keys($result))){
                    $model = MediaRecycle::findOne($id);
                    /* 需要保存的回收站数据 */
                    $model->result = MediaRecycle::RESULT_DELETED;
                    $model->status = MediaRecycle::STATUS_HANDLED;
                    $model->handled_by = \Yii::$app->user->id;
                    $model->handled_at = time();
                    if($model->save()){
                        $mediaModel = Media::findOne($model->media_id);
                        $mediaModel->del_status = Media::DEL_STATUS_LOGIC;
                        $mediaModel->save(true, ['del_status']);
                    }
                }
                
                $trans->commit();  //提交事务
                Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Operation Succeeded'));
                return new ApiResponse(ApiResponse::CODE_COMMON_OK);
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error', Yii::t('app', 'Operation Failed:') . $ex->getMessage());
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            }
        }
        
        return $this->renderAjax('____handle_result', [
            'ids' => json_encode($ids)
        ]);
    }
}
