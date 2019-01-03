<?php

namespace backend\modules\media_config\controllers;

use yii\web\Controller;

/**
 * Default controller for the `media_config` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
}
