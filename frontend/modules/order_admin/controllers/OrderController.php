<?php

namespace frontend\modules\order_admin\controllers;

use common\models\api\ApiResponse;
use common\models\order\Order;
use common\models\order\OrderAction;
use common\models\order\searchs\OrderGoodsSearch;
use common\models\order\searchs\OrderSearch;
use common\models\order\searchs\PlayApproveSearch;
use Yii;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['simple-view'],   //订单核查页不需要登录也可查看
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ]
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
            'resourcesData' => $resourcesData,  // 素材列表数据
            'filter' => Yii::$app->request->queryParams,
        ]);
    }
    
    /**
     * 确认素材可用
     * @param string $id
     * @return mixed
     */
    public function actionConfirm($id)
    {
        Yii::$app->getResponse()->format = 'json';
        
        $model = $this->findModel($id);
        $model->order_status = Order::ORDER_STATUS_CONFIRMED; //把订单状态改为取消状态
        $model->confirm_at = time();
        if($model->save()){
            OrderAction::savaOrderAction($id, '订单确认', '订单确认', $model->order_status, $model->play_status, Yii::$app->user->id);
        }

        return new ApiResponse(ApiResponse::CODE_COMMON_OK);
    }

    /**
     * 订单核查
     * @param int $order_sn
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionSimpleView($order_sn)
    {
        // 使用主布局main的布局样式
        $this->layout = '@app/views/layouts/main';
        
        $model = Order::findOne(['order_sn' => $order_sn]);
        $searchModel = new OrderGoodsSearch();
        $dataProvider = $searchModel->searchMedia(null, $order_sn);
        
        if(empty($model)){
            throw new NotFoundHttpException('未找到该订单，请确认订单编号是否正确！');
        }
        
        return $this->render('simple-view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'filters' => Yii::$app->request->queryParams,
        ]);
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
        if($model->save()){
            OrderAction::savaOrderAction($id, '取消订单', '取消订单', $model->order_status, $model->play_status, Yii::$app->user->id);
        }
        
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
