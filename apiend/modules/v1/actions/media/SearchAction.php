<?php

namespace apiend\modules\v1\actions\media;

use apiend\models\Response;
use apiend\modules\v1\actions\BaseAction;
use common\models\media\Acl;
use common\models\media\Dir;
use common\models\media\Media;
use Yii;
use yii\db\Query;

/**
 * 获取目录详细
 *
 * @author Administrator
 */
class SearchAction extends BaseAction {

    public function run() {
        if (!$this->verify()) {
            return $this->verifyError;
        }
        //指定搜索目录，默认为根目录
        $dir_id = $this->getSecretParam('dir_id', 0);
        //素材库，不能为空
        $category_id = $this->getSecretParam('category_id', null);
        //是否递归搜索
        $recursive = $this->getSecretParam('recursive', 1);
        //查找关键字
        $keyword = $this->getSecretParam('keyword', "");
        //每页显示多个素材
        $limit = $this->getSecretParam('limit', 20);
        //当前分页
        $page = $this->getSecretParam('page', 1);
        //指定要搜索的素材类型，多个用“,”分隔
        $type_id = $this->getSecretParam('type_id', "");

        //参数处理
        $keyword = str_replace(['，', '、', ',', ' '], '|', $keyword);          //转换为 k|k|k 格式
        $page = $page < 1 ? 1 : $page;
        $types = $type_id == "" ? [] : explode(',', $type_id);                  //转换 t,t,t 为 [t,t,t] 数组



        /* 检查必须参数 */
        $notfounds = $this->checkRequiredParams($this->getSecretParams(), ['category_id']);
        if (count($notfounds) > 0) {
            return new Response(Response::CODE_COMMON_MISS_PARAM, null, null, ['param' => implode(',', $notfounds)]);
        }

        /*
         * 建立查询
         */
        $query = (new Query())
                ->select(['Media.id', 'Media.name', 'Media.type_id', 'Media.cover_url', 'Media.tags', 'Media.created_at', 'Media.size', 'Media.visit_count', 'Media.download_count'])
                ->from(['Media' => Media::tableName()])
                ->leftJoin(['Dir' => Dir::tableName()], 'Media.dir_id = Dir.id')
                ->where(['Media.del_status' => 0, 'Media.category_id' => $category_id])
                ->andFilterWhere(['Media.type_id' => $types]);

        //-----------------------       
        // 目录过滤
        //-----------------------
        if (!$recursive) {
            //只在当前目录下搜索
            $query->andWhere(['Media.dir_id' => $dir_id]);
        } else {
            //递归搜索所有目录
            $dir = Dir::getDirById($dir_id);
            $query->andWhere(['or',
                ['like', 'Dir.path', $dir->path . ",%", false],
                ['Dir.path' => $dir->path]]);
        }
        //-----------------------
        // 关键字过滤
        //-----------------------
        if (!empty($keyword)) {
            $query->andFilterWhere(['OR', ['REGEXP', 'Media.name', $keyword], ['REGEXP', 'Media.tags', $keyword]]);
        }

        $queryClone = clone $query;
        //查询出所有值
        $query->offset(($page - 1) * $limit)
                ->limit($limit)
                ->orderBy(['Media.download_count' => SORT_DESC, 'Media.name' => SORT_ASC]);

        //生成临时访问路径和下载路径
        $temp_link_url = Yii::$app->params['media']['use']['temp_link_url'];
        $temp_download_url = Yii::$app->params['media']['use']['temp_download_url'];
        $medias = $query->all();
        foreach($medias as &$media){
            $sn = Acl::getTempSnByMid($media['id']);
            $media['url'] = "$temp_link_url?sn={$sn}";
            $media['download_url'] = "$temp_download_url?sn={$sn}";
        }
        return new Response(Response::CODE_COMMON_OK, null, [
            'page' => $page,
            'total_count' => (int) $queryClone->select(['Media.id'])->count(),
            'list' => $medias,
        ]);
    }

}
