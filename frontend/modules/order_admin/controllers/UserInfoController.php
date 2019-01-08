<?php

namespace frontend\modules\order_admin\controllers;

use common\models\order\Order;
use common\models\order\searchs\OrderSearch;
use common\models\User;
use common\models\UserProfile;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

/**
 * UserInfoController implements the CRUD actions for Cart model.
 */
class UserInfoController extends Controller
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    /**
     * 更改用户信息页面
     * @return type
     */
    public function actionSetting()
    {       
        $userModel = $this->findUserModel(Yii::$app->user->id);
        $userModel->scenario = User::SCENARIO_UPDATE;
        $peofileModel = $this->findProfileModel(Yii::$app->user->id);

        if($userModel->id != Yii::$app->user->id){
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        // 判断更改的数据
        $sets = ArrayHelper::getValue(Yii::$app->request->queryParams, 'set', 'base');
        if($sets == 'base'){
            // 基础数据
            if ($userModel->load(Yii::$app->request->post())) {
                $userModel->save();
            }
        } else {
            // 附加数据
            if ($peofileModel->load(Yii::$app->request->post())) {
                $peofileModel->save();
            }
        }
        
        return $this->render('setting', [
            'userModel' => $userModel,
            'peofileModel' => $peofileModel,
            'sets' => $sets,
            'filter' => Yii::$app->request->queryParams,
        ]);

    }
    
    /**
     * 个人统计页面
     * @return type
     */
    public function actionStatistics()
    {
        /* @var $request Request */
        $request = Yii::$app->getRequest();
        $dateRange = $request->getQueryParam('dateRange');
        $year = $request->getQueryParam('year');
        
        $query = (new \yii\db\Query())
                ->from(['Order' => Order::tableName()]);
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
//        var_dump($this->getTotalPay($query));exit;
//        var_dump($this->getDataStatistics(Yii::$app->request->queryParams));exit;
        return $this->render('statistics', [
            'dateRange' => $dateRange,
            'totalPay' => $this->getTotalPay($query),                 //总支出和购买数量
        ]);
    }
    
    
    /**
     * 根据其主键值查找 User 模型。
     * 如果找不到模型，就会抛出404个HTTP异常。
     * @param string $id
     * @return model User 
     * @throws NotFoundHttpException
     */
    protected function findUserModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    /**
     * 根据其主键值查找 UserProfile 模型。
     * 如果找不到模型，就会抛出404个HTTP异常。
     * @param string $id
     * @return model User 
     * @throws NotFoundHttpException
     */
    protected function findProfileModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    protected function getTotalPay($query)
    {
        $totalPay = clone $query;
        $totalPay->select(['SUM(order_amount) AS total_price', 'SUM(goods_num) AS total_goods']);
        
        return $totalPay->one(Yii::$app->db);
    }

    protected function getDataStatistics($query)
    {
        $searchModel = new OrderSearch();
        
        $query->addSelect(['SUM(order_amount) AS total_price', 'SUM(goods_num) AS total_goods']);
        
//        $query->groupBy(['Order.id']);
        $results = $query->asArray()->all();
        
        return $results;
    }
}

