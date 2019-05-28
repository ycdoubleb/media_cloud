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
    var zTreeDropdown = function(element, options){
        var _self = this;
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
            var _self = this;
            _self._initzTree();
            _self._setDefaultValue();
            // 单击显示下拉列表
            _self.plug_id.find('span.zTree-dropdown-selection').bind("click", function(){
                _self.showTree();
            });
        },
        
        /**
         * 初始化机构树状插件
         * @returns {undefined}
         */
        _initzTree: function(){
            var _self = this;
            var treeId = _self.plug_id.find('ul#' + _self.options.tree_id);
            $.fn.zTree.init(treeId, _self.options.tree_config, _self.options.tree_data);
        },
        
        /**
         * 设置默认值
         * @returns {undefined}
         */
        _setDefaultValue: function(){
            var _self = this;
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
         * @param {type} element
         * @returns {undefined}
         */
        _onBodyDownByActionType: function(event, element){
            var _self = element;
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
            var _self = this;
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
            var _self = this;
            _self.plug_id.find('input[type="hidden"]').val(value);
            _self.plug_id.find('span.zTree-dropdown-selection').html(text);
        },
        
        /**
         * 下拉框显示
         * @param {type} elem
         * @returns {undefined}
         */
        showTree: function(){
            var _self = this;
            var ztree = _self.plug_id.find("div." + _self.options.tree_class);
            // 显示或隐藏树状列表
            if(ztree.css('display') == 'none'){
                ztree.css('display','block'); 
            } else{
                ztree.css('display','none'); 
            }

            $("body").bind("mousedown", function(event){
                _self._onBodyDownByActionType(event, _self)
            }); 
        },
        
        /**
         * 下拉框隐藏
         * @returns {Boolean}
         */
        hideTree: function(){
            var _self = this;
            var ztree = _self.plug_id.find("div." + _self.options.tree_class);
            // 隐藏树状列表
            ztree.css('display','none'); 

            $("body").unbind("mousedown", function(event){
                _self._onBodyDownByActionType(event, _self)
            });

            return false;
        },
        
        /**
         * 添加鼠标进过显示编辑图标
         * @param {type} treeId
         * @param {type} treeNode
         * @returns {undefined}
         */
        addHoverDom: function(treeId, treeNode){
            var _self = this;
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
            var _self = this;
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
            var _self = this;
            var plugParents = _self.plug_id.parents('div.form-group');
            // 如果是模型规则调用，则执行
            if(plugParents.length > 0 && plugParents.hasClass('has-error')){
                _self.plug_id.parents('div.form-group').removeClass('has-error').addClass('has-success');
                plugParents.find('div.help-block').html('');
            }
            
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
            var _self = this;
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
            var _self = this;
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
        var ztreeDropdown = new zTreeDropdown(this, options)
        return ztreeDropdown;
    }
    
})(jQuery,window,document);