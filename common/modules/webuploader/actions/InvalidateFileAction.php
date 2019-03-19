<?php

namespace common\modules\webuploader\actions;

use common\modules\webuploader\models\Uploadfile;
use common\modules\webuploader\models\UploadfileChunk;
use common\modules\webuploader\models\UploadResponse;
use Exception;
use Yii;

/**
 * 使指定file无效，以便文件可重新上传
 * 设置 uploadfile,uploadfile_chunk 已删除
 *
 * @author Administrator
 */
class InvalidateFileAction extends BaseAction {

    public function run() {
        if (!isset($_REQUEST['fileMd5'])) {
            //不提供fileMd5...
            return new UploadResponse(UploadResponse::CODE_COMMON_MISS_PARAM, null, null, ['param' => 'fileMd5']);
        }
        $fileMd5 = $_REQUEST['fileMd5'];

        $tran = Yii::$app->db->beginTransaction();
        try {
            Uploadfile::updateAll(['is_del' => 1], ['md5' => $fileMd5, 'is_del' => 0]);
            $fileChunks = UploadfileChunk::findAll(['file_md5' => $fileMd5, 'is_del' => 0]);
            try {
                //删除临时文件
                foreach ($fileChunks as $fileChunk) {
                    if (file_exists($fileChunk->chunk_path)) {
                        @unlink($fileChunk->chunk_path);
                    }
                }
            } catch (\Exception $ex) {}

            //删除数据库分片数据记录
            UploadfileChunk::updateAll(['is_del' => 1], ['file_md5' => $fileMd5]);

            $tran->commit();
        } catch (Exception $ex) {
            $tran->rollBack();
        }
        return new UploadResponse(UploadResponse::CODE_COMMON_OK);
    }

}
