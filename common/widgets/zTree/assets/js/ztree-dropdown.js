//自定义下拉框集成ztree
//机构树
/*传入参数tree:用于承接ztree的ul的id;treeDivId:用于承接ztree的div的id;inputId:接受显示选中文字的input的id;
	     inputHideId:接受选中文字对应的id的input的id（一个hidden的input）;treeDataList:形成树形需要的数据数组；
*/
(function (win, $) {
    var _self;
    
    var zTreeDropdown = function(){}
    
    /**
     * 初始化
     * @param {type} data
     * @returns {undefined}
     */
    zTreeDropdown.prototype.init = function(data){
        _self = this;
        
        _self.dropdown_id = data.dropdown;       //下拉框id
        _self.value = data.value,                //下拉框值
        _self.placeholder = data.placeholder;    //提示
        _self.tree_id  = data.treeid;            //树状下拉框的id
        _self.tree_class = data.class;           //树状下拉框的样式名
        _self.tree_config = data.config;         //树状下拉框的配置
        _self.tree_data = data.dataList;         //树状下拉框的初始数据
        _self.url = data.url;
                
        //初始化树状插件
        _self.__initzTree();    
        
        _self.__setDefaultValue();
    }
    
    /**
     * 下拉框显示
     * @returns {undefined}
     */
    zTreeDropdown.prototype.showTree = function(){
        // 显示或隐藏树状列表
        if($("." + _self.tree_class).css('display') == 'none'){
            $("." + _self.tree_class).css('display','block'); 
        } else{
            $("." + _self.tree_class).css('display','none'); 
        }
         
        $("body").bind("mousedown", _self.__onBodyDownByActionType); 
    }
    
    /**
     * 下拉框隐藏
     * @returns {Boolean}
     */
    zTreeDropdown.prototype.hideTree = function() {          
        // 隐藏树状列表
        $("." + _self.tree_class).css('display','none'); 
        
        $("body").unbind("mousedown", _self.__onBodyDownByActionType); 
        
        return false;
    } 
    
    /**
     * 添加鼠标进过显示编辑图标
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {undefined}
     */
    zTreeDropdown.prototype.addHoverDom = function(treeId, treeNode) {        
        var sObj = $("#" + treeNode.tId + "_span");
        if (treeNode.editNameFlag || $("#addBtn_" + treeNode.tId).length > 0)
            return;
        var addStr = "<span class='button add' id='addBtn_" + treeNode.tId + "' title='add node' onfocus='this.blur();'></span>";
        sObj.after(addStr);
        var btn = $("#addBtn_" + treeNode.tId);
        if (btn){
            btn.bind("click", function () {
                var treeObj = $.fn.zTree.getZTreeObj(treeId);
                
                // 动态加载子级目录
                _self.__dynamicallyLoadingSubDir(treeId, treeNode);
                
                // 设置定时执行
                setTimeout(function(){
                    // 添加新目录
                    $.post(_self.url.create,{parent_id: treeNode.id}, function(response){
                        if(response.code == "0"){
                            var data = $.extend({isParent: true}, response.data);
                            treeObj.addNodes(treeNode, data);
                            _self.setVoluation(response.data.id, response.data.name);
                        }
                    });
                }, 300);


                return false;
            });
        }
    }    

    /**
     * 移除鼠标进过显示编辑图标
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {undefined}
     */
    zTreeDropdown.prototype.removeHoverDom = function (treeId, treeNode) {
        $("#addBtn_" + treeNode.tId).unbind().remove();
    }


    /**
     * 点击展开项, 添加节点 
     * @param {type} event
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {undefined}
     */
    zTreeDropdown.prototype.zTreeOnExpand = function (event,treeId, treeNode) {                
        _self.__dynamicallyLoadingSubDir(treeId, treeNode);
    }

    /**
     * 节点点击事件
     * @param {type} event
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {undefined}
     */
    zTreeDropdown.prototype.zTreeOnClick = function (event, treeId, treeNode) {     
        _self.setVoluation(treeNode.id, treeNode.name);
        _self.hideTree();  
    };

    /**
     * 编辑节点名前
     * @param {type} treeId
     * @param {type} treeNode
     * @param {type} newName
     * @param {type} isCancel
     * @returns {Boolean}
     */
    zTreeDropdown.prototype.zTreeBeforeRename = function (treeId, treeNode, newName, isCancel) {    
        $.ajax({
            type: 'post',
            async: false,
            url: _self.url.update,
            data: {id: treeNode.id, name: newName},
            success: function(response){
                if(response.code != "0" && response.data == null){
                    isCancel = false;
                    $.notify({
                        message: response.msg,    //提示消息
                    },{
                        type: "danger", //错误类型
                    });
                }else{
                    _self.setVoluation(response.data.id, response.data.name);
                    isCancel = true;
                }
            }
        });
        
        return isCancel;
    }

    /**
     * 删除节点前
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {Boolean}
     */
    zTreeDropdown.prototype.zTreeBeforeRemove = function (treeId, treeNode) {        
        $.post(_self.url.delete, {id: treeNode.id}, function(response){
            if(response.code != "0"){
                $.notify({
                    message: response.msg,    //提示消息
                },{
                    type: "danger", //错误类型
                });
            }else{
                var treeObj = $.fn.zTree.getZTreeObj(treeId);
                var parentNode = treeNode.getParentNode();  // 获取父级节点
                treeObj.removeNode(treeNode);
                if(parentNode.children.length == 0) { 
                    parentNode.isParent = true;
                    treeObj.updateNode(parentNode); 
                }
                
                _self.setVoluation('', $('<span class="zTree-dropdown-selection__placeholder"/>').text(_self.placeholder));
            }
        });      

        return false;
    }

    /**
     * 给下拉框设置选中的值
     * @param {int} value
     * @param {string} text
     * @returns {undefined}
     */
    zTreeDropdown.prototype.setVoluation = function(value, text){
        $("#" + _self.dropdown_id).val(value);
        $("#" + _self.dropdown_id + "-text").html(text);
    }


    /**
     * 设置默认值
     * @returns {undefined}
     */
    zTreeDropdown.prototype. __setDefaultValue = function(){
        //如果值非空则设置默认值
        if(!!_self.value){
            var treeObj = $.fn.zTree.getZTreeObj(_self.tree_id);
            var nodes = treeObj.getNodes();
            for(var i in nodes){
                if(nodes[i].id == _self.value){
                    _self.setVoluation(nodes[i].id, nodes[i].name);
                    break;
                }
            }
        }
    }

    /**
     * 动态加载子级目录(第一次展开的时候)
     * @param {type} treeId
     * @param {type} treeNode
     * @returns {undefined}
     */
    zTreeDropdown.prototype.__dynamicallyLoadingSubDir = function(treeId, treeNode){        
        var treeObj = $.fn.zTree.getZTreeObj(treeId);
        var parentZNode = treeObj.getNodeByParam("id", treeNode.id, null);//获取指定父节点
        var childNodes = treeObj.transformToArray(treeNode);//获取子节点集合
        
        //childNodes.length 小于等于1，就加载(第一次加载)
        if(childNodes.length <= 1){
            $.get(_self.url.view, {id: treeNode.id}, function(response){
                if(response.data.length > 0){
                    treeObj.addNodes(parentZNode, response.data, false);     //添加节点     
                }
            });
        }
    }

    /**
     * 区域外点击事件
     * @param {type} event
     * @param {type} _self
     * @returns {undefined}
     */
    zTreeDropdown.prototype.__onBodyDownByActionType = function(event) {  
        if (event.target.id.indexOf(_self.tree_id) == -1){  
            if(event.target.id != 'selectDevType'){
                _self.hideTree(); 
            } 
        }  
    }
    
    /**
     * 初始化插件
     * @returns {undefined}
     */
    zTreeDropdown.prototype.__initzTree = function(){
        $.fn.zTree.init($("#" + this.tree_id), this.tree_config, this.tree_data);
    }
    
    win.zTree = win.zTreeDropdown || {};
    win.zTree.zTreeDropdown = zTreeDropdown;
    
})(window, jQuery);



