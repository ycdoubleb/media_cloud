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
        
        return $this->render('link',[
            'model' => $acl
        ]);
    }
}