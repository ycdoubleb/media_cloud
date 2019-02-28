<?php

namespace backend\modules\media_admin\controllers;

use common\components\redis\RedisService;
use common\models\media\Acl;
use Yii;
use yii\helpers\ArrayHelper;
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
        $dir = $_POST['filename'];
        $dir = "uploads/" . md5($dir);
        file_exists($dir) or mkdir($dir, 0777, true);
        $path = $dir . "/" . $_POST['blobname'];
        move_uploaded_file($_FILES["file"]["tmp_name"], $path);
        
        if(isset($_POST['lastone'])){
            echo $_POST['lastone'];  
            $count=$_POST['lastone'];  
            
            $fp   = fopen($_POST['filename'],"abw");  
            
            for($i = 0; $i <= $count; $i++){
                $handle = fopen($dir . '/' . $i, 'rb');
                fwrite($fp, fread($handle, filesize($dir . '/' . $i)));
                fclose($handle);
            }
            fclose($fp);
        }
        
        return $this->render('index');
    }

}
