//自定义下拉框集成ztree
//机构树
/*传入参数tree:用于承接ztree的ul的id;treeDivId:用于承接ztree的div的id;inputId:接受显示选中文字的input的id;
	     inputHideId:接受选中文字对应的id的input的id（一个hidden的input）;treeDataList:形成树形需要的数据数组；
*/
var treeId;    // 插件id
var treeName;  // 下拉选中显示的名
var treeValue; // 下拉选中显示的值

function zTreeDropdown(zTree, zTreeName, zTreeValue, treeConfig, treeDataList){
	treeId = zTree;
        treeName = zTreeName;
        treeValue = zTreeValue;
        
	var treeDataList = treeDataList;
	var setConfig = {
            view: {
                addHoverDom: addHoverDom,
                removeHoverDom: removeHoverDom,
            },
            data: {
                simpleData: {
                    enable: true
                }
            },
            edit: {
                enable: true
            },
            //回调
            callback: {
                onClick: zTreeOnClick,
                onExpand: zTreeOnExpand,
                onRename: zTreeOnRename,
                beforeRemove: zTreeBeforeRemove
            }
	};								
	
        treeConfig = $.extend(setConfig, treeConfig);
       
        initzTree(treeId, treeConfig, treeDataList)
}

// 初始化
function initzTree(zTree, treeConfig, treeDataList){
    $.fn.zTree.init($("#" + zTree), treeConfig, treeDataList);
}

// 添加鼠标进过显示编辑图标
function addHoverDom(treeId, treeNode) {
    var sObj = $("#" + treeNode.tId + "_span");
    if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
        return;
    var addStr = "<span class='button add' id='addBtn_" + treeNode.tId + "' title='add node' onfocus='this.blur();'></span>";
    sObj.after(addStr);
    var btn = $("#addBtn_" + treeNode.tId);
    if (btn){
        btn.bind("click", function () {
            var zTree = $.fn.zTree.getZTreeObj(treeId);
            var parentZNode = zTree.getNodeByParam("id", treeNode.id, null);//获取指定父节点
            var childNodes = zTree.transformToArray(treeNode);//获取子节点集合
            //childNodes.length 小于等于1，就加载(第一次加载)
            if(childNodes.length <= 1){
                $.get('/media_config/dir/search-children?id=' + treeNode.id, function(response){
                    if(response.data.length > 0){
                        zTree.addNodes(parentZNode, response.data, false);     //添加节点     
                    }
                });
            }
            // 设置定时执行
            setTimeout(function(){
                // 添加新目录
                $.post('/media_config/dir/add-dynamic',{parent_id: treeNode.id}, function(response){
                    if(response.code == "0"){
                        var data = $.extend({isParent: true}, response.data);
                        zTree.addNodes(treeNode, data);
                        $('#'+treeName).html(response.data.name);
                        $('#'+treeValue).val(response.data.id);
                    }
                });
            }, 300);
            
            
            return false;
        });
    }
}    

// 移除鼠标进过显示编辑图标
function removeHoverDom(treeId, treeNode) {
    $("#addBtn_" + treeNode.tId).unbind().remove();
}

//下拉框显示 隐藏
function showTree(){
    if($('.ztree').css('display') == 'none'){
        $('.ztree').css('display','block'); 
     } else{
         $('.ztree').css('display','none'); 
     }
     $("body").bind("mousedown", onBodyDownByActionType); 
 }
 
 // 下拉框隐藏
function hideTree() {  
    $('.ztree').css('display','none');
    $("body").unbind("mousedown", onBodyDownByActionType); 
    return false;
} 

//区域外点击事件
function onBodyDownByActionType(event) {  
    if (event.target.id.indexOf(treeId) == -1){  
        if(event.target.id != 'selectDevType'){
            hideTree(); 
        } 
    }  
}

//点击展开项, 添加节点  第一次展开的时候
function zTreeOnExpand(event,treeId, treeNode) {   
    var treeObj = $.fn.zTree.getZTreeObj(treeId);
    var parentZNode = treeObj.getNodeByParam("id", treeNode.id, null);//获取指定父节点
    var childNodes = treeObj.transformToArray(treeNode);//获取子节点集合
    //childNodes.length 小于等于1，就加载(第一次加载)
    if(childNodes.length <= 1){
        $.get('/media_config/dir/search-children?id=' + treeNode.id, function(response){
            if(response.data.length > 0){
                treeObj.addNodes(parentZNode, response.data, false);     //添加节点     
            }
        });
    }
} 

//节点点击事件
function zTreeOnClick(event, treeId, treeNode) {
    $('#'+treeName).html(treeNode.name);
    $('#'+treeValue).val(treeNode.id);
    hideTree();  
};

// 编辑节点名
function zTreeOnRename(event, treeId, treeNode, isCancel) {
    $.post('/media_config/dir/edit-dynamic',{id: treeNode.id, name: treeNode.name}, function(response){
        if(response.code == "0" && response.data != null){
            $('#'+treeName).html(response.data.name);
            $('#'+treeValue).val(response.data.id)
        }else{
            alert(response.msg);
        }
    });
}
    
// 删除节点前
function zTreeBeforeRemove(treeId, treeNode) {
    $.post('/media_config/dir/delete?id=' + treeNode.id, function(response){
        if(response.code != "0"){
            alert(response.msg);
        }else{
            var treeObj = $.fn.zTree.getZTreeObj(treeId);
            var parentNode = treeNode.getParentNode();
            treeObj.removeNode(treeNode);
            if(parentNode.children.length == 0) { 
                parentNode.isParent = true;
                treeObj.updateNode(parentNode); 
            }
            $('#'+treeName).html('<span class="zTree-dropdown-selection__placeholder">全部</span>');
            $('#'+treeValue).val('');
        }
    });      

    return false;
}