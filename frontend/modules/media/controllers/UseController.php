<?php

namespace frontend\modules\media\controllers;

use common\models\media\Acl;
use Yii;
use yii\web\Controller;

/**
 * UseController implements the CRUD actions for Acl model.
 */
class UseController extends Controller
{
    /**
     * Lists all Acl models.
     * @return mixed
     */
    public function actionLink($sn)
    {
        $acl = Acl::getAclInfoBySn($sn);

        if(empty($acl)){
            throw new \yii\web\NotFoundHttpException('找不到对应的媒体！');
        }
        $url = $acl['url']."?".http_build_query(Yii::$app->request->getQueryParams());
        
        return $this->redirect($url);
    }
}