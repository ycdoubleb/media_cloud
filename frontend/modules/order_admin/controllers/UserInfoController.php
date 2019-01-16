<?php

namespace frontend\modules\order_admin\controllers;

use common\models\order\Order;
use common\models\User;
use common\models\UserProfile;
use Yii;
use yii\db\Query;
use yii\filters\AccessControl;
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
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ]
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
        $year = $request->getQueryParam('year') == null ? date('Y',time()) : $request->getQueryParam('year');

        return $this->render('statistics', [
            'dateRange' => $dateRange,
            'year' => $year,
            'years' => $this->getOrderYears(),
            'totalPay' => $this->getTotalPay($dateRange),           //总支出和购买数量
            'dateStatistics' => $this->getDateStatistics($year),    //年度月支出金额
        ]);
    }
    
    
    /**
     * 根据其主键值查找 User 模型。
     * 如果找不到模型，就会抛出404个HTTP异常。
     * @param int $id
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
     * @param int $id
     * @return model UserProfile 
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
    
    /**
     * 时间段支出金额
     * @param string $dateRange 时间段
     * @return array
     */
    protected function getTotalPay($dateRange)
    {
        $query = (new Query())
                ->select(['SUM(order_amount) AS total_price', 'SUM(goods_num) AS total_goods'])
                ->where(['order_status' => Order::ORDER_STATUS_CONFIRMED])
                ->from(['Order' => Order::tableName()]);
        
        // 当时间段参数不为空时
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        return $query->one();
    }

    /**
     * 年度月支出金额
     * @param string $year  年份
     * @return array
     */
    protected function getDateStatistics($year)
    {
        $query = (new Query())
                ->select(["CONCAT(FROM_UNIXTIME(confirm_at, '%c'), '月') AS name",
                    'SUM(order_amount) AS value'])
                ->where(['order_status' => Order::ORDER_STATUS_CONFIRMED])
                ->from(['Order' => Order::tableName()]);
        // 当年份参数不为空时
        if($year != null){
            $startTime = $year.'-01-01 00:00:00';
            $endTime = $year.'-12-31 23:59:59';
        }
        $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($startTime), strtotime($endTime)]);
        
        $query->groupBy('name');    // 按月份分组
        $results = $query->all();
        $months = [
            ['name' => '1月', 'value' => 0],
            ['name' => '2月', 'value' => 0],
            ['name' => '3月', 'value' => 0],
            ['name' => '4月', 'value' => 0],
            ['name' => '5月', 'value' => 0],
            ['name' => '6月', 'value' => 0],
            ['name' => '7月', 'value' => 0],
            ['name' => '8月', 'value' => 0],
            ['name' => '9月', 'value' => 0],
            ['name' => '10月', 'value' => 0],
            ['name' => '11月', 'value' => 0],
            ['name' => '12月', 'value' => 0],
        ];
        
        $merges = ArrayHelper::merge(ArrayHelper::index($months, 'name'), ArrayHelper::index($results, 'name'));
        $items = [];
        foreach ($merges as $merge){
            $items[] = $merge;
        }
        
        return $items;
    }
    
    /**
     * 获取订单确认年份
     * @return array
     */
    protected function getOrderYears()
    {
        $query = (new Query())
                ->select(['confirm_at', "FROM_UNIXTIME(confirm_at, '%Y') AS years"])
                ->where(['order_status' => Order::ORDER_STATUS_CONFIRMED])
                ->from(['Order' => Order::tableName()])
                ->groupBy('years')
                ->all();

        return ArrayHelper::map($query, 'years', 'years');
    }
}

