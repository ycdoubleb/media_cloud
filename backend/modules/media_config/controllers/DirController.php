<?php

namespace backend\modules\media_config\controllers;

use common\models\media\Dir;
use common\models\media\searchs\DirSearh;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * DirController implements the CRUD actions for Dir model.
 */
class DirController extends Controller
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
     * 列出所有媒体存储目录配置.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DirSearh();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => Dir::getDirListFramework(),
        ]);
    }

    /**
     * 显示单个媒体存储目录配置.
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 创建 媒体存储目录配置
     * 如果创建成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $model = new Dir(['created_by' => \Yii::$app->user->id,]);
        $model->loadDefaultValues();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /** 开启事务 */
            $trans = Yii::$app->db->beginTransaction();
            try {
                $is_submit = false;
                if($id !== null) $model->parent_id = $id;
                if($model->save()){
                    $is_submit = true;
                    $model->updateParentPath();
                    Dir::invalidateCache();
                }else{
                    Yii::$app->getSession()->setFlash('error', '保存失败::' . implode('；', $model->getErrorSummary(true)));
                }
                
                if($is_submit) $trans->commit();  //提交事务
                
            } catch (Exception $ex) {
                $trans ->rollBack(); //回滚事务
                Yii::$app->getSession()->setFlash('error','操作失败::'.$ex->getMessage());
            }
            
            return $this->redirect(['index']);
        }

        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * 更新 媒体存储目录配置
     * 如果更新成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $targetModel = Dir::getDirById($model->parent_id); //目标模型
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
     * 删除 媒体存储目录配置
     * 如果删除成功，浏览器将被重定向到“index”页面。
     * @param string $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        Yii::$app->getResponse()->format = 'json';
        $results = [
            'code' => 10002,
            'data' => ['id' => $model->id, 'name' => $model->name],
            'message' => Yii::t('app', 'You have no permissions to perform this operation.'),
        ];
        
        if(count(Dir::getDirsChildren($id)) > 0){
            $results['message'] = '该目录下存在子目录，不能删除。';
            return $results;
        }else if(count($model->medias) > 0) {
            $results['message'] = '该目录下存在媒体素材，不能删除。';
            return $results;
        }else{
            $model->delete();
            Dir::invalidateCache();    //清除缓存
            $results['code'] = 0;
            $results['message'] = '操作成功！';
        }

        return $results;
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
        $dirsChildren = Dir::getDirsChildren($id); 
        $childrens = [];
        foreach ($dirsChildren as $index => $item) {
            if($target_id != null){
                if($target_id == $item['id']){
                    unset($item[$index]);
                    break;
                }
            }
            $childrens[] = $item;
        }
        
        Yii::$app->getResponse()->format = 'json';
        return [
            'result' => 1,
            'data' => $childrens,
        ];
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
