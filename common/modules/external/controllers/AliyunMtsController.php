<?php

namespace common\modules\external\controllers;

use common\components\aliyuncs\AliyunMtsService;
use common\components\aliyuncs\MediaAliyunAction;
use Exception;
use Yii;
use yii\base\Object;
use yii\web\Controller;

/**
 * Aliyun 转码服务回调
 *
 * @author Administrator
 */
class AliyunMtsController extends Controller {

    public $enableCsrfValidation = false;

    public function actionTaskComplete() {
        try {
            $post = \Yii::$app->request->post();
            //$post = json_decode('{\"jobId\":\"1a0659d9f6ff4349a0d5aeeef6dd6875\",\"requestId\":\"22D0C51A-E363-408B-A337-CE58B47F32E4\",\"Type\":\"Transcode\",\"state\":\"Success\",\"UserData\":\"{\\\"created_by\\\":\\\"0\\\",\\\"redirect_url\\\":\\\"http://tt.mediacloud.studying8.com/external/aliyun-mts/task-complete\\\",\\\"level\\\":0,\\\"media_id\\\":\\\"1\\\",\\\"is_redirect\\\":1}\",\"type\":\"Transcode\",\"userData\":\"{\\\"created_by\\\":\\\"0\\\",\\\"redirect_url\\\":\\\"http://tt.mediacloud.studying8.com/external/aliyun-mts/task-complete\\\",\\\"level\\\":0,\\\"media_id\\\":\\\"1\\\",\\\"is_redirect\\\":1}\",\"State\":\"Success\",\"JobId\":\"1a0659d9f6ff4349a0d5aeeef6dd6875\",\"RequestId\":\"22D0C51A-E363-408B-A337-CE58B47F32E4\"}');
            //var_dump($post);
            $topicName = $post['TopicName'];
        } catch (Exception $ex) {
            $topicName = "";
        }
        
        if ($topicName != Yii::$app->params['aliyun']['mts']['topic_name']) {
            return; //过滤无用信息
        }
        switch (json_decode($post['Message'])->Type) {
            case 'Transcode':
                $this->trancode($post);
                break;
            case 'Snapshot':
                //$this->trancode($post);
                break;
        }
        try {
            
        } catch (Exception $ex) {
            Yii::info(__FUNCTION__, $post);
            Yii::error(__FUNCTION__, $ex->getMessage() . "\n" . $ex->getTraceAsString());
        }
    }

    /**
     * 转码完成回调
     * @param Object $post
     */
    private function trancode($post) {
        $message = json_decode($post['Message']);         //信息体
        $jobId = $message->JobId;                       //任务ID
        $requestId = $message->RequestId;               //请求ID
        $state = $message->State;                       //状态 Success,Fail
        $userData = json_decode($message->UserData);    //自定义数据 [level,video_id,created_by]
        
        //更新转码服务记录
        $mtsService = AliyunMtsService::findOne(['job_id' => $jobId]);
        if ($mtsService == null) {
            return;
        }
        $mtsService->result = ($state == 'Success' ? 1 : 0);
        $mtsService->is_finish = 1;
        $mtsService->save(false, ['result', 'is_finish']);
        
        //整合视频转码
        $result = MediaAliyunAction::integrateVideoTrancode($userData->media_id);
    }

    /**
     * 视频截图回调
     * 
     * @param Object $post
     */
    private function snapshot($post) {
        //...
    }

}
