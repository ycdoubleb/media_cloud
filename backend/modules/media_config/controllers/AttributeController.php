<?php

namespace backend\modules\media_config\controllers;

use common\models\media\MediaAttribute;
use common\models\media\searchs\MediaAttributeSearch;
use common\models\media\searchs\MediaAttributeValueSearch;
use common\widgets\grid\GridViewChangeSelfController;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;




/**
 * AttributeController implements the CRUD actions for MediaAttribute model.
 */
class AttributeController extends GridViewChangeSelfController
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
     * 列出所有素材属性配置.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MediaAttributeSearch();
        $results = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'filters' => $results['filter'],     //查询过滤
            'totalCount' => $results['total'],     //计算总数量
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['attribute'],
                'key' => 'id',
            ]),
            'category_id' => ArrayHelper::getValue(\Yii::$app->request->queryParams, 'category_id')
        ]);
    }

    /**
     * 显示单个素材属性配置。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $searchModel = new MediaAttributeValueSearch(['attribute_id' => $id]);
        $results = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('view', [
            'model' => $this->findModel($id),
            'filters' => $results['filter'],     //查询过滤
            'totalCount' => $results['total'],     //计算总数量
            'dataProvider' => new ArrayDataProvider([
                'allModels' => $results['data']['attVal'],
                'key' => 'id',
            ]),
        ]);
    }

    /**
     * 创建 素材属性配置
     * 如果创建成功，浏览器将被重定向到“index”页面。
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MediaAttribute();
        $model->loadDefaultValues();
        //分库id
        $model->category_id = ArrayHelper::getValue(\Yii::$app->request->queryParams, 'category_id');
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新 素材属性配置
     * 如果更新成功，浏览器将被重定向到“index”页面。
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

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除 素材属性配置
     * 如果删除成功，浏览器将被重定向到“index”页面。
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
     * 根据其主键值查找模型。
     * 如果找不到模型，就会抛出404 HTTP异常。
     * @param string $id
     * @return MediaAttribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MediaAttribute::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
