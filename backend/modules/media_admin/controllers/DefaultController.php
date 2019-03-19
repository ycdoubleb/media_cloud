<?php

namespace backend\modules\media_admin\controllers;

use common\models\media\Dir;
use Yii;
use yii\web\Controller;

/**
 * Default controller for the `media_admin` module
 */
class DefaultController extends Controller {

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        
        $dir_dom = [];
        $dirBySameLevels = Dir::getDirsBySameLevel(null, Yii::$app->user->id, true);
        foreach ($dirBySameLevels as $dirLists) {
            foreach ($dirLists as $dir) {
                $dir['isParent'] = true;
                $dir_dom[] = $dir;
            }    
        }
        
        return $this->render('index', [
            'dataProvider' => json_encode($dir_dom)
        ]);
    }
}
