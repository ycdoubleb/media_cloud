<?php

namespace frontend\modules\media\controllers;

use common\models\log\MediaVisitLog;
use common\models\log\UserVisitLog;
use common\models\media\Acl;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * UseController implements the CRUD actions for Acl model.
 */
class UseController extends Controller {

    /**
     * Lists all Acl models.
     * @return mixed
     */
    public function actionLink($sn) {
        //['id', 'url', 'media_id', 'user_id', 'visit_count']
        $acl = Acl::getAclInfoBySn($sn, ['url', 'media_id', 'order_id', 'user_id']);
        if (empty($acl)) {
            throw new NotFoundHttpException('找不到对应的媒体！');
        }
        $url = $acl['url'] . "?" . http_build_query(Yii::$app->request->getQueryParams());

        //增加媒体访问总量
        Acl::visitIncrby($sn);
        //记录媒体月访问量
        MediaVisitLog::visitIncrby($acl['media_id']);
        //记录用户访问量
        UserVisitLog::visitIncrby($acl['order_id'], $acl['user_id']);

        return $this->redirect($url);
    }

    /**
     * 临时访问
     * @param string $sn
     */
    public function actionTempLink($sn) {
        $url = Acl::getTempUrlBySn($sn);
        if (empty($url)) {
            throw new NotFoundHttpException('找不到对应的媒体！');
        }
        $url = $url . "?" . http_build_query(Yii::$app->request->getQueryParams());
        return $this->redirect($url);
    }

}
