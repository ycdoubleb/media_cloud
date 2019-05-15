/**
 * 自定义下拉框集成ztree
 * 传入参数tree:用于承接ztree的ul的id;treeDivId:用于承接ztree的div的id;inputId:接受显示选中文字的input的id;
 * inputHideId:接受选中文字对应的id的input的id（一个hidden的input）;treeDataList:形成树形需要的数据数组
 * @param {type} $
 * @param {type} window
 * @param {type} document
 * @returns {undefined}
 */
;(function($,window,document){
    var _self;
    var zTreeDropdown = function(element, options){
        _self = this;
        _self.$elem = $(element);
        _self.defaults = {
            plug_id: null,
            value: null,
            placeholder: null,
            tree_id: null,
            tree_class: null,
            tree_config: {},
            tree_data: {},
            url: {
                index: null,
                view: null,
                create: null,
                update: null,
                delete: null,
            },
        }
        
        // 插件id
        _self.plug_id = _self.defaults.plug_id != null ? $("#" + _self.defaults.plug_id) : _self.$elem;
        // 合并所有配置
        _self.options = $.extend({},_self.defaults, options);
        // 初始化
        _self.init();
    }
    
    // 插件属性
    zTreeDropdown.prototype = {
        /**
         * 初始化
         * @returns {undefined}
         */
        init: function(){
            _self._initzTree();
            _self._setDefaultValue();
        },
        
        /**
         * 初始化机构树状插件
         * @returns {undefined}
         */
        _initzTree: function(){
            var treeId = $('#' + _self.options.tree_id);
            $.fn.zTree.init(treeId, _self.options.tree_config, _self.options.tree_data);
        },
        
        /**
         * 设置默认值
         * @returns {undefined}
         */
        _setDefaultValue: function(){
            //如果值非空则设置默认值
            if(!!_self.options.value){
                // 如果子级树状是动态获取则需要动态获取对应目录设置赋值
                if(_self.options.url.view){
                    $.get(_self.options.url.view, {id: _self.options.value}, function(response){
                        _self.setVoluation(response.data.id, response.data.name);
                    });
                }else{
                    var treeObj = $.fn.zTree.getZTreeObj(_self.options.tree_id);
                    var nodes = treeObj.getNodes();

                    for(var i in nodes){
                        if(nodes[i].id == _self.options.value){
                            _self.setVoluation(nodes[i].id, nodes[i].name);
                            break;
                        }
                    }
                }
            }
        },
        
        /**
         * 区域外点击事件
         * @param {type} event
         * @returns {undefined}
         */
        _onBodyDownByActionType: function(event){
            if (event.target.id.indexOf(_self.options.tree_id) == -1){  
                if(event.target.id != 'selectDevType'){
                    _self.hideTree(); 
                } 
            }  
        },
        
        /**
         * 动态加载子级目录(第一次展开的时候)
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        _dynamicallyLoadingSubDir: function(treeId, treeNode){
            var treeObj = $.fn.zTree.getZTreeObj(treeId);
            var parentZNode = treeObj.getNodeByParam("id", treeNode.id, null);//获取指定父节点
            var childNodes = treeObj.transformToArray(treeNode);//获取子节点集合

            //childNodes.length 小于等于1，就加载(第一次加载)
            if(childNodes.length <= 1){
                $.get(_self.options.url.index, {id: treeNode.id}, function(response){
                    if(response.data.length > 0){
                        treeObj.addNodes(parentZNode, response.data, false);     //添加节点     
                    }
                });
            }
        },
        
        /**
         * 给下拉框设置选中的值
         * @param {type} value
         * @param {type} text
         * @returns {undefined}
         */
        setVoluation: function(value, text){
            _self.plug_id.siblings('input[type="hidden"]').val(value);
            _self.plug_id.html(text);
        },
        
        /**
         * 下拉框显示
         * @param {type} elem
         * @returns {undefined}
         */
        showTree: function(){
            var ztree = _self.plug_id.siblings("div." + _self.options.tree_class);
            // 显示或隐藏树状列表
            if(ztree.css('display') == 'none'){
                ztree.css('display','block'); 
            } else{
                ztree.css('display','none'); 
            }

            $("body").bind("mousedown", _self._onBodyDownByActionType); 
        },
        
        /**
         * 下拉框隐藏
         * @returns {Boolean}
         */
        hideTree: function(){
            var ztree = _self.plug_id.siblings("div." + _self.options.tree_class);
            // 隐藏树状列表
            ztree.css('display','none'); 

            $("body").unbind("mousedown", _self._onBodyDownByActionType); 

            return false;
        },
        
        /**
         * 添加鼠标进过显示编辑图标
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        addHoverDom: function(treeId, treeNode){
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
                    _self._dynamicallyLoadingSubDir(treeId, treeNode);

                    // 设置定时执行
                    setTimeout(function(){
                        // 添加新目录
                        $.post(_self.options.url.create,{parent_id: treeNode.id}, function(response){
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
        },
        
        /**
         * 移除鼠标进过显示编辑图标
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        removeHoverDom: function(treeId, treeNode){
            $("#addBtn_" + treeNode.tId).unbind().remove();
        },
        
        /**
         * 点击展开项, 添加节点 
         * @param {type} event
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        zTreeOnExpand: function(event,treeId, treeNode){
            _self._dynamicallyLoadingSubDir(treeId, treeNode);
        },
        
        /**
         * 节点点击事件
         * @param {type} event
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        zTreeOnClick: function(event, treeId, treeNode){
            _self.setVoluation(treeNode.id, treeNode.name);
            _self.hideTree();  
        },
        
        /**
         * 编辑节点名前
         * @param {type} treeId
         * @param {type} treeNode
         * @param {type} newName
         * @param {type} isCancel
         * @returns {Boolean}
         */
        zTreeBeforeRename: function(treeId, treeNode, newName, isCancel){
            $.ajax({
                type: 'post',
                async: false,
                url: _self.options.url.update,
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
        },
        
        /**
         * 删除节点前
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {Boolean}
         */
        zTreeBeforeRemove: function(treeId, treeNode){
            $.post(_self.options.url.delete, {id: treeNode.id}, function(response){
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

                    _self.setVoluation('', $('<span class="zTree-dropdown-selection__placeholder"/>').text(_self.options.placeholder));
                }
            });      

            return false;
        }
    }
    
    /**
     * 返回插件实例
     * @param options
     * @returns {zTreeDropdown}
     */
    $.fn.ztreeDropdown = function(options){
        return new zTreeDropdown(this, options);
    }
    
})(jQuery,window,document);