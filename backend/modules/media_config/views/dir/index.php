<?php

use common\models\media\searchs\DirSearh;
use common\widgets\tabselfcolumn\TabSelfColumnAssets;
use wbraganca\fancytree\FancytreeWidget;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;

/* @var $this View */
/* @var $searchModel DirSearh */
/* @var $dataProvider ActiveDataProvider */

TabSelfColumnAssets::register($this);

$this->title = Yii::t('app', 'Storage Dir');

$this->params['breadcrumbs'][] = Yii::t('app', '{Dir}{List}', [
    'Dir' => Yii::t('app', 'Dir'), 'List' => Yii::t('app', 'List')
]);

?>
<div class="dir-index">
   
    <div class="col-lg-12 col-md-12" style="margin-bottom: 10px;">
        
        <div class="pull-left">
            <?= Html::a(Yii::t('app', '{Create}{Dir}', [
                'Create' => Yii::t('app', 'Create'), 'Dir' => Yii::t('app', 'Dir')
            ]), ['create', 'category_id' => $category_id], ['id' => 'btn-addDir', 'class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('app', '{Move}{Dir}', [
                'Move' => Yii::t('app', 'Move'), 'Dir' => Yii::t('app', 'Dir')
            ]), ['move', 'category_id' => $category_id], ['id' => 'btn-moveDir', 'class' => 'btn btn-primary']) ?>
        </div>
        
    </div>
        
    <?= FancytreeWidget::widget([
        'options' =>[
            'id' => 'table-fancytree_1', // 设置整体id
            'checkbox' => true,
            'selectMode' => 3,
            'source' => $dataProvider,
            'extensions' => ['table', 'dnd'],
            'table' => [
                'indentation' => 20,
                'nodeColumnIdx' => 0
            ],
            'select' => new JsExpression('function(event, data){
                var node = data.node,
                    level = node.getLevel(),
                    pList = node.getParentList();
                for(i in pList){
                    if(level != pList[i].getLevel()){
                        pList[i].selected = false;
                        $(pList[i].tr).removeClass("fancytree-selected");
                    }
                }
            }'),
            'renderColumns' => new JsExpression('function(event, data) {
                //初始化组件
                var tabColumn = new tabcolumn.TabSelfColumn();
                var node = data.node;
                var $span =  tabColumn.init({
                    data:{key: node.key,fieldName:"is_del",value:node.data.is_del,dome:"this"}
                });
                $(node.tr).find(">td.is_del").html($span);
                //设置a标签的属性
                $(node.tr).find(">td.btn_groups a").each(function(index){
                    var _this = $(this);
                    switch(index){
                        case 0:
                            var url = _this.attr({href: _this.attr("href") + "&id=" + node.key});
                            _this.click(function(e){
                                e.preventDefault();
                                showModal(_this);
                            });
                            break;
                        case 1:
                            _this.attr({href: _this.attr("href") + "&id=" + node.key});
                            _this.click(function(e){
                                e.preventDefault();
                                showModal(_this);
                            });
                            break;
                        case 2:
                            _this.click(function(e){
                                e.preventDefault();
                                if(confirm("您确定要删除此项吗？") == true){
                                    $.post(_this.attr("href") + "?id=" + node.key, function(response){
                                        if(response.code == 0){
                                            node.remove();
                                        }else{
                                            alert(response.msg);
                                        }
                                    });
                                }
                            });
                            break;
                    }
                });
            }'),
        ]
    ]); ?>
    
    <div class="table-responsive col-lg-12 col-md-12">
        <table id="table-fancytree_1" class="table table-bordered table-hover detail-view">
            <colgroup>
                <col width="700px"></col>
                <col width="45px"></col>
                <col width="55px"></col>
            </colgroup>
            <thead>
              <tr>
                  <th><?= Yii::t('app', 'Name') ?></th>
                  <th><?= Yii::t('app', 'Is Del') ?></th>
                  <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="name" style="text-align: left;"></td>
                    <td class="is_del" style="text-align: center;"></td>
                    <td class="btn_groups" style="text-align: center;">
                        <?php
                            echo Html::a('<span class="glyphicon glyphicon-plus"></span>', [
                                'create', 'category_id' => $category_id
                            ], ['title' => Yii::t('app', 'Create')]) . '&nbsp;';
                            echo Html::a('<span class="glyphicon glyphicon-pencil"></span>', [
                                'update', 'category_id' => $category_id
                            ], ['title' => Yii::t('app', 'Update')]) . '&nbsp;';     
                            echo Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete'], ['title' => Yii::t('app', 'Delete')]);     
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
</div>

<!--加载模态框-->
<?= $this->render('/layouts/modal'); ?>

<?php
$js = <<<JS
    
    // 弹出添加素材存储目录面板
    $('#btn-addDir').click(function(e){
        e.preventDefault();
        showModal($(this));
    });
    // 弹出移动素材存储目录结构面板
    $('#btn-moveDir').click(function(e){
        e.preventDefault();
        var vals = [];
        var selectedNodes = [];
        var is_public = false;
        //获取所有选中的节点
        $("#table-fancytree_1").fancytree("getRootNode").visit(function(node) {
            if(node.isSelected()){
                selectedNodes = node.tree.getSelectedNodes();
            }
        });
        //组装移动目录id数组
        for(i in selectedNodes){
            vals.push(selectedNodes[i].key);
        }
        if(vals.length > 0){
            var url = $(this).attr('href') + '&move_ids=' + vals
            $(".myModal").html("");
            $('.myModal').modal("show").load(url);
        }else{
            alert("请选择移动的目录。");
        }
    });
    
JS;
    $this->registerJs($js, View::POS_READY);
?>
