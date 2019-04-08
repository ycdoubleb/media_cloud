<?php

namespace frontend\modules\media\controllers;

use common\models\log\MediaVisitLog;
use common\models\log\UserVisitLog;
use common\models\media\Acl;
use common\models\media\Media;
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
            throw new NotFoundHttpException('找不到对应的素材！');
        }
        $url = $acl['url'] . "?" . http_build_query(Yii::$app->request->getQueryParams());

        //增加素材访问总量
        Acl::visitIncrby($sn);
        //记录素材月访问量
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
        $tempInfo = Acl::getTempInfoBySn($sn);
        if (empty($tempInfo)) {
            throw new NotFoundHttpException('找不到对应的素材！');
        }
        // 查看次数+1
        Media::updateAllCounters(['visit_count' => 1 ], ['id' => $tempInfo['media_id']]);
        $url = $tempInfo['url'] . "?" . http_build_query(Yii::$app->request->getQueryParams());
        return $this->redirect($url);
    }
    
    /**
     * 临时下载
     * @param string $sn
     */
    public function actionTempDownload($sn) {
        $tempInfo = Acl::getTempInfoBySn($sn);
        if (empty($tempInfo)) {
            throw new NotFoundHttpException('找不到对应的素材！');
        }
        // 下载次数+1
        Media::updateAllCounters(['download_count' => 1 ], ['id' => $tempInfo['media_id']]);
        $url = $tempInfo['url'] . "?" . http_build_query(Yii::$app->request->getQueryParams());
        return $this->redirect($url);
    }

}
