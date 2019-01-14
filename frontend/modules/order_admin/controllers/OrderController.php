<?php

namespace frontend\modules\order_admin\controllers;

use common\models\order\Order;
use common\models\order\searchs\OrderGoodsSearch;
use common\models\order\searchs\OrderSearch;
use common\models\order\searchs\PlayApproveSearch;
use Yii;
use yii\data\ArrayDataProvider;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
//                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->searchOrder(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }

    /**
     * Displays a single Order model.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $auditingSearch = new PlayApproveSearch();
        $auditingData = $auditingSearch->searchDetails($id, Yii::$app->request->queryParams);

        $resourcesSearch = new OrderGoodsSearch();
        $resourcesData = $resourcesSearch->searchMedia($id);
        
        return $this->render('view', [
            'model' => $this->findModel($id),   // Order模型
            'auditingData' => $auditingData,    // 支付审核数据
            'resourcesData' => $resourcesData,  // 资源列表数据
            'filter' => Yii::$app->request->queryParams,
        ]);
    }
    
    /**
     * 确认资源可用
     * @param string $id
     * @return mixed
     */
    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->order_status = Order::ORDER_STATUS_CONFIRMED; //把订单状态改为取消状态
        $model->confirm_at = time();
        $model->save();
        
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
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
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->order_status = Order::ORDER_STATUS_CANCELLED; //把订单状态改为取消状态
        $model->save();
        
        return $this->redirect(['index']);
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
