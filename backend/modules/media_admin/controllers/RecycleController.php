<?php

namespace backend\modules\media_admin\controllers;

use common\models\media\Media;
use common\models\media\MediaRecycle;
use common\models\media\searchs\MediaRecycleSearch;
use Yii;
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
        $searchModel = new MediaRecycleSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤的属性
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['recycles'],
                'key' => 'id',
                'pagination' => [
                    'defaultPageSize' => 10
                ]
            ]),
            'userMap' => ArrayHelper::map($results['data']['users'], 'id', 'nickname'),
        ]);
    }

    /**
     * 还原 媒体数据
     * 如果还原成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionRecovery()
    {
        // 所有id
        $ids = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'));  

        //查找已经存在的
        $result = MediaRecycle::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
        $result = ArrayHelper::index($result, 'id');

        foreach($ids as $id){
            // 过滤已经存在
            if(!isset($result[$id])){
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
        }
        
        Yii::$app->getSession()->setFlash('success','还原成功！');

        return $this->redirect(['index']);
    }

    /**
     * 删除 媒体数据
     * 如果还原成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        // 所有id
        $ids = explode(',', ArrayHelper::getValue(Yii::$app->request->queryParams, 'id'));  
        
        //查找已经存在的
        $result = MediaRecycle::find()->where(['id' => $ids, 'status' => 1])->asArray()->all();
        $result = ArrayHelper::index($result, 'id');
        
        foreach($ids as $id){
            // 过滤已经存在
            if(!isset($result[$id])){
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
        }
        
        Yii::$app->getSession()->setFlash('success','删除成功！');

        return $this->redirect(['index']);
    }
}
