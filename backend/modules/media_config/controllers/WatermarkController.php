<?php

namespace backend\modules\media_config\controllers;

use common\models\searchs\WatermarkSearch;
use common\models\Watermark;
use common\widgets\grid\GridViewChangeSelfController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * WatermarkController implements the CRUD actions for Watermark model.
 */
class WatermarkController extends GridViewChangeSelfController
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
     * 列出所有水印模型。
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WatermarkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $dataProvider,
                'key' => 'id',
                'pagination' => [
                    'defaultPageSize' => 10
                ]
            ]),
        ]);
    }

    /**
     * 显示单个水印模型。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 创建一个新的水印模型。
     * 如果创建成功，浏览器将被重定向到“视图”页面。
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Watermark();
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新现有的水印模型。
     * 如果更新成功，浏览器将被重定向到“视图”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除现有的水印模型。
     * 如果删除成功，浏览器将被重定向到“索引”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }
    
    /**
     * 启用 现有的 CustomerWatermark 模型。
     * @param integer $id          id
     * @param string $fieldName   字段名
     * @param integer $value       新值
     */
    public function actionEnable($id, $fieldName, $value)
    {
        $count = Watermark::find()->where(['is_del' => 0])->count('id');
        
        if(!$value && $count >= 20){
            Yii::$app->getResponse()->format = 'json';
            return [
                'result' => 0,
                'message' => '启用的数量已经达到最大数20条。'
            ];
        }
        
        parent::actionChangeValue($id, $fieldName, $value);
    }

    /**
     * 基于主键值查找水印模型。
     * 如果找不到模型，将引发404 HTTP异常。
     * @param string $id
     * @return Watermark the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Watermark::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
