<?php

namespace backend\modules\statistics\controllers;

use common\models\AdminUser;
use common\models\log\MediaVisitLog;
use common\models\media\Media;
use common\models\order\Order;
use common\models\order\OrderGoods;
use common\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Request;

/**
 * Default controller for the `statistic_admin` module
 */
class RankingStatisticsController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        /* @var $request Request */
        $request = Yii::$app->getRequest();
        $year = $request->getQueryParam('year') == null ? '' : $request->getQueryParam('year');
        $month = $request->getQueryParam('month') == null ? '' : $request->getQueryParam('month');

        return $this->render('index', [
            'operator' => $this->getAmountByOperator($year, $month),   //运营人
            'purchaser' => $this->getAmountByPurchaser($year, $month), //购买人
            'income' => $this->getAmountByMedia($year, $month),        //素材收入
            'click' => $this->getClickByMedia($year, $month),          //素材学习量
            'quote' => $this->getQuoteByMedia($year, $month),          //素材引用量

            'year' => $year,
            'month' => $month,
            'years' => $this->getYears(),
            'months' => $this->getMonths(),
            'tabs' => ArrayHelper::getValue(Yii::$app->request->queryParams, 'tabs', 'operator'),    // 过滤条件tabs
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }
    
    /**
     * 根据运营人收入金额统计
     * @param array $year   年份
     * @param array $month  月份
     * @return ArrayDataProvider
     */
    protected function getAmountByOperator($year, $month)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['SUM(OrderGoods.amount) AS value'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');

        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 运营人总收入金额
        $totalAmount = clone $query;
        $totalResults = $totalAmount->one();
        
        // 运营人饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['AdminUser.nickname AS name']);
        $chartsData->groupBy('Media.owner_id');
        $chartsResult = $chartsData->all();
        
        // 运营人表格数据
        $listsData = clone $query;
        $totalResult = empty($totalResults['value']) ? 1 : $totalResults['value'];
        $listsData->addSelect(['AdminUser.nickname AS name', "(SUM(OrderGoods.amount) / $totalResult) AS proportion"]);
        $listsData->groupBy('Media.owner_id');
        $listsData->orderBy(['proportion' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);

        $resluts = [
            'totalAmount' => empty($totalResults['value']) ? 0 : $totalResults['value'],
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];
        
        return $resluts;
    }
    
    /**
     * 根据购买人支出金额统计
     * @param array $year   年份
     * @param array $month  月份
     * @return ArrayDataProvider
     */
    protected function getAmountByPurchaser($year, $month)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['SUM(Order.order_amount) AS value'])
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by');
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 购买人总支出金额
        $totalAmount = clone $query;
        $totalResults = $totalAmount->one();
        
        // 购买人饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['User.nickname AS name']);
        $chartsData->groupBy('Order.created_by');
        $chartsResult = $chartsData->all();
        
        // 购买人表格数据
        $listsData = clone $query;
        $totalResult = empty($totalResults['value']) ? 1 : $totalResults['value'];
        $listsData->addSelect(['User.nickname AS name', "SUM(Order.order_amount) / $totalResult AS proportion"]);
        $listsData->groupBy('Order.created_by');
        $listsData->orderBy(['proportion' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);
        
        $resluts = [
            'totalAmount' => empty($totalResults['value']) ? 0 : $totalResults['value'],
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];
        
        return $resluts;
    }
    
    /**
     * 根据素材收入金额统计
     * @param array $year   年份
     * @param array $month  月份
     * @return ArrayDataProvider
     */
    protected function getAmountByMedia($year, $month)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['SUM(OrderGoods.amount) AS value'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 素材收入总金额
        $totalAmount = clone $query;
        $totalResults = $totalAmount->one();
        
        // 素材收入前20名的总金额
        $limitAmount = clone $query;
        $limitAmount->groupBy('Media.id');
        $limitAmount->limit(20);
        $limitResult = 0;
        foreach ($limitAmount->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 素材收入饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 素材收入表格数据
        $listsData = clone $query;
        $listsData->addSelect(['Media.id', 'Media.name', 'AdminUser.nickname']);
        $listsData->groupBy('Media.id');
        $listsData->orderBy(['value' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);
        
        $resluts = [
            'totalAmount' => empty($totalResults['value']) ? 0 : $totalResults['value'],
            'limitAmount' => $limitResult,
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];
        
        return $resluts;
    }
    
    /**
     * 根据素材学习量统计
     * @param array $year   年份
     * @param array $month  月份
     * @return ArrayDataProvider
     */
    protected function getClickByMedia($year, $month)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['Media.id','MediaVisitLog.visit_count AS value'])
                ->from(['MediaVisitLog' => MediaVisitLog::tableName()])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的素材
                ->leftJoin(['Media' => Media::tableName()], 'Media.id = MediaVisitLog.media_id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(MediaVisitLog.visit_time, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(MediaVisitLog.visit_time, '%m')" => $month]);
        }
        
        // 素材总学习量
        $totalClick = clone $query;
        $totalResults = 0;
        foreach ($totalClick->groupBy('Media.id')->all() as $value) {
            $totalResults += $value['value'];
        }
        
        // 素材学习量前20名的总量
        $limitClick = clone $query;
        $limitClick->groupBy('Media.id');
        $limitClick->limit(20);
        $limitResult = 0;
        foreach ($limitClick->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 素材学习量饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 素材学习量表格数据
        $listsData = clone $query;
        $listsData->addSelect(['Media.id', 'Media.name', 'AdminUser.nickname']);
        $listsData->groupBy('Media.id');
        $listsData->orderBy(['value' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);
        
        $resluts = [
            'totalClick' => $totalResults,
            'limitClick' => $limitResult,
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];
        
        return $resluts;
    }
    
    /**
     * 根据素材引用次数统计
     * @param array $year   年份
     * @param array $month  月份
     * @return ArrayDataProvider
     */
    protected function getQuoteByMedia($year, $month)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['COUNT(Order.id) AS value'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的素材
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');
        
        /* 当年份/月份参数不为空时 */
        if($year != null || $month != null){
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%Y')" => $year]);
            $query->andFilterWhere(["FROM_UNIXTIME(Order.created_at, '%m')" => $month]);
        }
        
        // 素材总引用次数
        $totalQuote = clone $query;
        $totalResults = $totalQuote->one();
        
        // 素材引用次数前20名的总量
        $limitQuote = clone $query;
        $limitQuote->groupBy('Media.id');
        $limitQuote->limit(20);
        $limitResult = 0;
        foreach ($limitQuote->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 素材引用次数饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 素材引用次数表格数据
        $listsData = clone $query;
        $listsData->addSelect(['Media.id', 'Media.name', 'AdminUser.nickname']);
        $listsData->groupBy('Media.id');
        $listsData->orderBy(['value' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);
        
        $resluts = [
            'totalQuote' => $totalResults['value'],
            'limitQuote' => $limitResult,
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];

        return $resluts;
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
