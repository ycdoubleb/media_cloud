<?php

namespace backend\modules\statistics\controllers;

use common\models\AdminUser;
use common\models\media\Acl;
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
        $dateRange = $request->getQueryParam('dateRange');

        return $this->render('index', [
            'operator' => $this->getAmountByOperator($dateRange),   //运营人
            'purchaser' => $this->getAmountByPurchaser($dateRange), //购买人
            'income' => $this->getAmountByMedia($dateRange),        //媒体收入
            'click' => $this->getClickByMedia($dateRange),          //媒体点击量
            'quote' => $this->getQuoteByMedia($dateRange),          //媒体引用量

            'tabs' => ArrayHelper::getValue(Yii::$app->request->queryParams, 'tabs', 'operator'),    // 过滤条件tabs
            'dateRange' => $dateRange,
            'filters' => Yii::$app->request->queryParams,  //过滤条件
        ]);
    }
    
    /**
     * 根据运营人收入金额统计
     * @param array $dateRange 时间段
     * @return ArrayDataProvider
     */
    protected function getAmountByOperator($dateRange)
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
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
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
     * @param array $dateRange 时间段
     * @return ArrayDataProvider
     */
    protected function getAmountByPurchaser($dateRange)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['SUM(Order.order_amount) AS value'])
                ->from(['Order' => Order::tableName()])
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['User' => User::tableName()], 'User.id = Order.created_by');
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
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
     * 根据媒体收入金额统计
     * @param array $dateRange 时间段
     * @return ArrayDataProvider
     */
    protected function getAmountByMedia($dateRange)
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
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        // 媒体收入总金额
        $totalAmount = clone $query;
        $totalResults = $totalAmount->one();
        
        // 媒体收入前20名的总金额
        $limitAmount = clone $query;
        $limitAmount->groupBy('Media.id');
        $limitAmount->limit(20);
        $limitResult = 0;
        foreach ($limitAmount->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 媒体收入饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 媒体收入表格数据
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
     * 根据媒体点击量统计
     * @param array $dateRange 时间段
     * @return ArrayDataProvider
     */
    protected function getClickByMedia($dateRange)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['SUM(Acl.visit_count) AS value'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的媒体
                ->leftJoin(['Acl' => Acl::tableName()], 'Acl.media_id = Media.id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Acl.updated_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        // 媒体总点击量
        $totalClick = clone $query;
        $totalResults = $totalClick->one();
        
        // 媒体点击量前20名的总量
        $limitClick = clone $query;
        $limitClick->groupBy('Media.id');
        $limitClick->limit(20);
        $limitResult = 0;
        foreach ($limitClick->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 媒体点击量饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 媒体点击量表格数据
        $listsData = clone $query;
        $listsData->addSelect(['Media.id', 'Media.name', 'AdminUser.nickname']);
        $listsData->groupBy('Media.id');
        $listsData->orderBy(['value' => SORT_DESC]);   //倒序
        $listsResult = $listsData->all();
        $dataProvider = new ArrayDataProvider([
            'allModels' => array_values($listsResult),
        ]);
        
        $resluts = [
            'totalClick' => empty($totalResults['value']) ? 0 : $totalResults['value'],
            'limitClick' => $limitResult,
            'chartsData' => $chartsResult,
            'listsData' => $dataProvider,
        ];
        
        return $resluts;
    }
    
    /**
     * 根据媒体引用次数统计
     * @param array $dateRange 时间段
     * @return ArrayDataProvider
     */
    protected function getQuoteByMedia($dateRange)
    {
        /* @var $query Query */
        $query = (new Query())
                ->select(['COUNT(Order.id) AS value'])
                ->from(['Media' => Media::tableName()])
                ->andFilterWhere(['Media.status' => Media::STATUS_PUBLISHED])   //已发布的媒体
                ->andFilterWhere(['Order.order_status' => 
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]])   //待确认和已确认的订单
                ->leftJoin(['OrderGoods' => OrderGoods::tableName()], 'OrderGoods.goods_id = Media.id')
                ->leftJoin(['Order' => Order::tableName()], 'Order.id = OrderGoods.order_id')
                ->leftJoin(['AdminUser' => AdminUser::tableName()], 'AdminUser.id = Media.owner_id');
        
        /* 当时间段参数不为空时 */
        if($dateRange != null){
            $dateRange_Arr = explode(" - ",$dateRange);
            $query->andFilterWhere(['between', 'Order.confirm_at', strtotime($dateRange_Arr[0]), strtotime($dateRange_Arr[1])]);
        }
        
        // 媒体总引用次数
        $totalQuote = clone $query;
        $totalResults = $totalQuote->one();
        
        // 媒体引用次数前20名的总量
        $limitQuote = clone $query;
        $limitQuote->groupBy('Media.id');
        $limitQuote->limit(20);
        $limitResult = 0;
        foreach ($limitQuote->all() as $value) {
            $limitResult += $value['value'];
        }
        
        // 媒体点击量饼图数据
        $chartsData = clone $query;
        $chartsData->addSelect(['Media.name']);
        $chartsData->groupBy('Media.id');
        $chartsResult = $chartsData->all();
        
        // 媒体点击量表格数据
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
}
