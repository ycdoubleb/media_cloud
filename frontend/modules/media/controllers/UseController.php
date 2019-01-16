<?php

namespace frontend\modules\media\controllers;

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
    public function actionLink($id)
    {
        $acl = \common\models\media\Acl::findOne(['id' => $id, 'status' => 1]);
        $acl->visit_count = $acl->visit_count + 1;
        $acl->save();
        
        $url = $acl->url."?".http_build_query(\Yii::$app->request->getQueryParams());
        
        return $this->redirect($url);
    }
}