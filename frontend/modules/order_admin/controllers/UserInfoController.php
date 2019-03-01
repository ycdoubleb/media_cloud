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
        $allYear = $request->getQueryParam('allYear') == null ? '' : $request->getQueryParam('allYear');  //总数统计
        $allMonth = $request->getQueryParam('allMonth') == null ? '' : $request->getQueryParam('allMonth');   //总数统计
        $year = $request->getQueryParam('year') == null ? '' : $request->getQueryParam('year');         //年度月统计
        
        return $this->render('statistics', [
            'allYear' => $allYear,
            'allMonth' => $allMonth,
            'year' => $year,
            'years' => $this->getYears(),
            'months' => $this->getMonths(),
            'totalPay' => $this->getTotalPay($allYear, $allMonth),           //总支出和购买数量
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
        $model = User::findOne($id);
        
        if ($model != null) {
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
        $model = UserProfile::findOne(['user_id' => $id]);
        
        if ($model != null) {
            return $model;
        }else{
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
    
    /**
     * 时间段支出金额
     * @param type $allYear     年份    
     * @param type $allMonth    月份
     * @return array
     */
    protected function getTotalPay($allYear, $allMonth)
    {
        $query = (new Query())
                ->select(['SUM(order_amount) AS total_price', 'SUM(goods_num) AS total_goods'])
                ->where([
                    'order_status' => [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED],
                    'created_by' => Yii::$app->user->id
                ])
                ->from(['Order' => Order::tableName()]);
        
        // 当时间段参数不为空时
        if($allYear != null || $allMonth != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.confirm_at, '%Y')" => $allYear]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.confirm_at, '%m')" => $allMonth]);
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
                ->where([
                    'order_status' => [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED],
                    'created_by' => Yii::$app->user->id
                ])
                ->from(['Order' => Order::tableName()]);
        // 当年份参数不为空时
        if(!empty($year)){
            $startTime = $year.'-01-01 00:00:00';
            $endTime = $year.'-12-31 23:59:59';
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($startTime), strtotime($endTime)]);
        }
        
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
     * 年份
     * @return array
     */
    protected function getYears()
    {
        $startYear = 2019;                  // 平台开始使用年份
        $theYear = date('Y',time());        // 当前年份
        $addYear = $theYear - $startYear;   // 平台已经使用了N年
        
        $years = ['' => '全部'];
        for($i = 0; $i <= $addYear; $i++){
            $years += [
                $startYear + $i => $startYear + $i . '年'
            ];
        }
        
        return $years;
    }
    
    /**
     * 月份
     * @return array
     */
    protected function getMonths()
    {
        $month = [
            '' => '全年',
            '01' => '01月',
            '02' => '02月',
            '03' => '03月',
            '04' => '04月',
            '05' => '05月',
            '06' => '06月',
            '07' => '07月',
            '08' => '08月',
            '09' => '09月',
            '10' => '10月',
            '11' => '11月',
            '12' => '12月',
        ];
        
        return $month;
    }
    
}

