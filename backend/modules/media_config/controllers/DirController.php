<?php

namespace backend\modules\media_config\controllers;

use common\models\api\ApiResponse;
use common\models\media\Dir;
use common\widgets\grid\GridViewChangeSelfController;
use Yii;
use yii\db\Query;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * DirController implements the CRUD actions for Dir model.
 */
class DirController extends GridViewChangeSelfController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * 列出所有素材存储目录配置.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index', [
            'dataProvider' => Dir::getDirListFramework(),
        ]);
    }

    /**
     * 创建 素材存储目录配置
     * 如果创建成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     */
    public function actionCreate($id = 0)
    {
        if (Yii::$app->request->isPost) {
            // 返回json格式
            \Yii::$app->response->format = 'json';
            
            $dir_path = ArrayHelper::getValue(Yii::$app->request->post(), 'dir_path');
            Dir::checkIsTheDirExists($dir_path, $id);
            
            return new ApiResponse(ApiResponse::CODE_COMMON_OK);
        }

        return $this->renderAjax('create');
    }

    /**
     * 更新 素材存储目录配置
     * 如果更新成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if(empty($model->parent_id)) $model->parent_id = 0;
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {  
                $is_submit = false;
                $moveDirChildrens  = Dir::getDirsChildren($model->id, null, false, true);  //移动目录下所有子级
                if($model->save()){
                    $is_submit = true;
                    $model->updateParentPath();    //修改路径
                    Dir::invalidateCache();    //清除缓存
                    foreach($moveDirChildrens as $moveChildren){
                        //获取修改子集的Dir模型
                        $childrenModel = $this->findModel($moveChildren['id']);
                        $childrenModel->updateParentPath(); //修改子集路径
                        Dir::invalidateCache();    //清除缓存
                        //计算 "," 在字符串中出现的次数,
                        $childrenModel->level = substr_count($childrenModel->path, ',');
                        $childrenModel->update(true, ['level']);
                    }
                }else{
                    Yii::$app->getSession()->setFlash('success','保存失败::' . implode('；', $model->getErrorSummary(true)));
                }
                
                if($is_submit) $trans->commit();  //提交事务
                
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['index']);
        }

        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除 素材存储目录配置
     * 如果删除成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        Yii::$app->getResponse()->format = 'json';
       
        if(count(Dir::getDirsChildren($id, \Yii::$app->user->id)) > 0){
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, '该目录下存在子目录，不能删除。');
        }else if(count($model->medias) > 0) {
            $data = new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, '该目录下存在素材素材，不能删除。');
        }else{
            $model->delete();
            Dir::invalidateCache();    //清除缓存
            $data = new ApiResponse(ApiResponse::CODE_COMMON_OK, null, $model->toArray());
        }

        return $data;
    }
    
    /**
     * 移动 现有的目录结构。
     * 如果移动成功，浏览器将被重定向到“列表”页。
     * @param string $move_ids   移动id
     * @param string $target_id  目标id
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function actionMove($move_ids = null, $target_id = 0)
    {
        $move_ids = explode(',', $move_ids);
        
        if (Yii::$app->request->isPost) {
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            { 
                $is_submit = true;
                $targetModel = Dir::getDirById($target_id);  //目标模型        
                //获取所要移动的目录
                $moveDirs = Dir::find()->where(['id' => $move_ids])->orderBy(['path' => SORT_ASC])->all();   
                foreach ($moveDirs as $moveModel) {
                    //旧的父级目录路径
                    $old_parent_path = str_replace(' > '. $moveModel->name, '', $moveModel->getFullPath());
                    //如果移动的分类父级id不在所要移动的id数组里，则设置所要移动的父级id为目标id
                    if(!in_array($moveModel->parent_id, $move_ids)){
                        $moveModel->parent_id = $target_id;
                    }
                    //计算 "," 在字符串中出现的次数,
                    $moveModel->level = substr_count($moveModel->path, ',');
                    $moveModel->update(false, ['parent_id', 'level']);
                    $moveModel->updateParentPath(); //修改子级路径
                    Dir::invalidateCache();    //清除缓存
                }
                
                if($is_submit) $trans->commit();  //提交事务
                
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['index']);
        }
        
        return $this->renderAjax('move', [
            'move_ids' => implode(',', $move_ids),    //所选的目录id
            'dataProvider' => Dir::getDirListFramework($move_ids),    //用户自定义的目录结构
        ]);
    }
    
    /**
     * 获取 目录的子级
     * @param string $target_id
     * @param string $id
     */
    public function actionSearchChildren($target_id = null, $id){
        $dirsChildren = Dir::getDirsChildren($id, \Yii::$app->user->id); 
        $childrens = [];
        if(count($dirsChildren) > 0){
            foreach ($dirsChildren as $index => $item) {
                if($target_id != null){
                    if($target_id == $item['id']){
                        unset($item[$index]);
                        break;
                    }
                }
                $item['isParent'] = true;
                $childrens[] = $item;
            }
        }
        
        Yii::$app->getResponse()->format = 'json';
        
        return new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $childrens);
    }
    
    /**
     * 动态添加存储目录
     * @return json
     */
    public function actionAddDynamic()
    {
        $post = Yii::$app->request->post();
        
        if(Yii::$app->request->isPost){
            Yii::$app->getResponse()->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                $num = 1;
                $dirName = '';
                $parent_id = ArrayHelper::getValue($post, 'parent_id');     // 父级id
                // 获取已经存在的【新建目录】or【新建目录（number）】格式的目录名称
                $query = (new Query())->from([Dir::tableName()])->where(['parent_id' => $parent_id])
                    ->andWhere(['OR', ['name' => '新建目录'], ['REGEXP', 'name',"^新建目录（[0-9]+）"]])
                    ->orderBy(['name' => SORT_ASC]);
                $dirNameExisted = ArrayHelper::getColumn($query->all(), 'name');
    
                // 循环组装目录名称
                do{
                    $num++;
                    if(!in_array('新建目录', $dirNameExisted)){
                        $dirName = '新建目录';
                        break;
                    }else if(!in_array("新建目录（{$num}）", $dirNameExisted)){
                        $dirName = "新建目录（{$num}）";
                        break;
                    }else{
                        continue;
                    }
                }while ($num < 1000);
                
                $model = new Dir(['parent_id' => $parent_id, 'created_by' => \Yii::$app->user->id]);
                $model->level = Dir::getDirById($parent_id)->level + 1;
                $model->name = $dirName;

                if($model->save()){
                    $model->updateParentPath(); //修改路径
                    Dir::invalidateCache();    //清除缓存

                    $trans->commit();  //提交事务
                    return new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $model->toArray());
                }
                
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            } 
        }
    }
    
    /**
     * 动态编辑存储目录
     * @return json
     */
    public function actionEditDynamic()
    {
        $post = Yii::$app->request->post();
        
        if(Yii::$app->request->isPost){
            Yii::$app->getResponse()->format = 'json';
            
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try
            {
                $id = ArrayHelper::getValue($post, 'id');     // 父级id
                $name = ArrayHelper::getValue($post, 'name');   // 名称
                
                $model = Dir::findOne($id);
                $model->name = $name;

                if($model->save()){
                    $model->updateParentPath(); //修改路径
                    Dir::invalidateCache();    //清除缓存

                    $trans->commit();  //提交事务
                    return new ApiResponse(ApiResponse::CODE_COMMON_OK, null , $model->toArray());
                }
                
            }catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                return new ApiResponse(ApiResponse::CODE_COMMON_SAVE_DB_FAIL, $ex->getMessage(), $ex->getTraceAsString());
            } 
        }
    }

    /**
     * 更新表值
     * @param integer $id
     * @param string $fieldName
     * @param integer $value
     */
    public function actionChangeValue($id, $fieldName, $value)
    {
        parent::actionChangeValue($id, $fieldName, $value);
        Dir::invalidateCache();    //清除缓存
    }
    
    /**
     * 根据其主键值查找模型。
     * 如果找不到模型，就会抛出404 HTTP异常。
     * @param string $id
     * @return Dir the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Dir::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }
}
