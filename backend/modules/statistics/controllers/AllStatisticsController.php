<?php

namespace backend\modules\statistics\controllers;

use common\models\log\MediaVisitLog;
use common\models\media\Media;
use common\models\media\MediaType;
use common\models\order\Order;
use yii\db\Query;
use yii\web\Controller;

/**
 * Default controller for the `statistic_admin` module
 */
class AllStatisticsController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        
        return $this->render('index', [
            'total_media_num' => $this->getTotalMediaNumber(),
            'total_order_amount' => $this->getTotalOrderAmount(),
            'total_visit_num' => $this->getTotalVisitNumber(),
            'statistics_chart' => $this->getStatisticsByType()
        ]);
    }
    
    /**
     * 获取素材总数量
     * @return array
     */
    protected function getTotalMediaNumber()
    {
        $query = (new Query())
                ->select(['COUNT(id) AS total_media_num'])
                ->from(['Media' => Media::tableName()])
                ->where([
                    'status' => Media::STATUS_PUBLISHED, 'del_status' => 0
                ])->one();

        return $query;
    }
    
    /**
     * 获取总收入金额
     * @return array
     */
    protected function getTotalOrderAmount()
    {
        $query = (new Query())
                ->select(['SUM(order_amount) AS total_order_amount'])
                ->from(['Order' => Order::tableName()])
                ->where(['order_status' =>
                    [Order::ORDER_STATUS_TO_BE_CONFIRMED, Order::ORDER_STATUS_CONFIRMED]]
                )->one();

        return $query;
    }
    
    /**
     * 获取总学习量
     * @return array
     */
    protected function getTotalVisitNumber()
    {
        $query = (new Query())
                ->select(['MediaVisitLog.visit_count AS value'])
                ->from(['MediaVisitLog' => MediaVisitLog::tableName()])
                ->all();
        
        $totalResults = 0;
        foreach ($query as $value) {
            $totalResults += $value['value'];
        }
        
        return $totalResults;
    }
    
    /**
     * 获取统计饼图信息
     * @return array
     */
    protected function getStatisticsByType()
    {
        $query = (new Query())
                ->select(['MediaType.name', 'COUNT(Media.id) AS value'])
                ->from(['Media' => Media::tableName()])
                ->where(['status' => Media::STATUS_PUBLISHED, 'del_status' => 0])
                ->leftJoin(['MediaType' => MediaType::tableName()], 'MediaType.id = Media.type_id')
                ->groupBy('MediaType.id')
                ->all();

        return $query;
    }
}
