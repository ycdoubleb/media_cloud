<?php

namespace console\controllers;

use common\models\log\MediaVisitLog;
use common\models\log\UserVisitLog;
use common\models\media\Acl;
use yii\console\Controller;
use yii\console\ExitCode;

/**
 * 同步redis数据到mysql
 *
 * @author Administrator
 */
class SyncDbController extends Controller {

    /**
     * 更新 ACL 访问次数
     */
    public function actionUpdateAclVisitcount() {
        Acl::updateDirtyFromCache();
        return ExitCode::OK;
    }

    /**
     * 订定更新用户访问情况
     * 更新用户访问数（小时内峰值）
     * 
     * @return type
     */
    public function actionSyncUserVisit() {
        UserVisitLog::syncLogFromCache();
        return ExitCode::OK;
    }

    /**
     * 同步媒体访问次数
     */
    public function actionSyncMediaVisitcount() {
        MediaVisitLog::syncLogFromCache();
        return ExitCode::OK;
    }

}
