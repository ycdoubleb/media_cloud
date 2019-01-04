/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function (win, $) {
    
    //================================================================================================
    //
    // VideoData class
    //
    //================================================================================================
    /**
     * 素材信息模型
     * @param {int} id 
     * @param {array} data
     * @returns {video-batch-uploadL#7.VideoData}
     */
    var MediaData = function (id, data) {
        var _self = this;
        
        this.id = data.id
        this.media_id = data.media_id;                          //媒体id
        
        this.submit_status = 0;                             //提交状态 0/1/2 未提交/提交中/已提交
        this.submit_result = false;                         //提交结果 false/true 失败/成功
        this.submit_feedback = ''                           //提交反馈

        this.errors = {};                                   //错误 key:mes
        
    }
   
    /**
     * 发送更改事件
     * @returns {undefined}
     */
    MediaData.prototype.sentChangeEvent = function(){
        $(this).trigger('change');
    };
   
    /**
     * 获取错误汇总
     * @returns {string}
     */
    MediaData.prototype.getErrorSummary = function () {
        var _self = this;
        var errors = [];
        $.each(_self.errors, function (key, value) {
            errors.push(value);
        });
        return errors.join('\n');
    };
    
    /**
     * 验证所有必须属性
     * @returns {Boolean}
     */
    MediaData.prototype.validate = function(){
        return this.getErrorSummary() == "";
    };
    
    /**
     * 获取上传所需要格式
     * @returns {Object}
     */
    MediaData.prototype.getPostData = function(){
        return{
            // 媒体基本信息
            MediaApprove: {
                id: this.id,
                media_id: this.media_id, 
            }
        };
    };
    
    /**
     * 设置提交结果
     * @param {int} status
     * @param {bool} result
     * @param {string} feedback
     * @param {object} dbdata
     * @returns {void}
     */
    MediaData.prototype.setSubmitResult = function(status,result,feedback,dbdata){
        this.submit_status = status;
        this.submit_result = result;
        this.submit_feedback = feedback;
        this.id = result ? dbdata.id : null;
        this.sentChangeEvent();
    };
    
    
    
    
    //================================================================================================
    //
    // MediaBatchApprove class
    //
    //================================================================================================
    /**
     * 媒体批量导入控制器
     * @param {type} config
     * @returns {media-batch-uploadL#7.MediaBatchApprove}
     */
    function MediaBatchApprove(config) {
        this.config = $.extend({
            media_url: 'create', //添加素材             
            submit_force: false, //已提交的强制提交
            submit_common_params: {}, //提交公共参数，如 form
        }, config);
        //model
        this.medias = [];           //素材信息数据
        //vars
        this.is_submiting = false;  //是否提交中
        this.submit_index = -1;     //当前提交索引
    }

    //------------------------------------------------------
    // 提交数据
    //------------------------------------------------------
    /**
     * 提交下一个任务
     * @returns {void}
     */
    MediaBatchApprove.prototype.__submitNext = function () {
        var index = this.submit_index;
        if (index >= this.medias.length - 1) {
            //完成
            this.is_submiting = false;
            $(this).trigger('submitFinished');
        } else {
            this.submit_index = ++index;
            this.__submitMediaData(index, this.config['submit_force']);
        }
    }

    /**
     * 上传素材数据，创建素材
     * 
     * @param {int} index       需要上传的索引
     * @param {bool} force      已完成的是否需要强制提交 默认false
     * @returns {void}
     */
    MediaBatchApprove.prototype.__submitMediaData = function (index, force) {
        force = !!force;
        var _self = this;
        var md = this.medias[index];
        
        if (!md || (md.submit_status == 2 && md.submit_result)) {
            //找不到数据或者已经创建成功的 跳过
            this.__submitNext();
        } else {
            var postData = md.getPostData();
            if (md.validate()) {
                var submit_common_params = this.config['submit_common_params'];
                postData = $.extend(postData, submit_common_params);
                md.setSubmitResult(1);  //设置提交中
                $.post(this.config['media_url'], postData, function (response) {
                    try {
                        var feedback = "";
                        if (response.data.code == '10002') {
                            feedback = response.msg;     //其它错误显示
                        }
                        //code 不为0即为失败
                        md.setSubmitResult(2, response.data.code == "0", feedback, response.data.data);
                    } catch (e) {
                        if (console) {
                            console.error(e);
                        }
                        md.setSubmitResult(2, false, '未知错误');
                    }

                    $(_self).trigger('submitCompleted', md);         //发送单个素材上传完成
                    _self.__submitNext();
                });
            } else {
                this.__submitNext();
            }
        }
    }
    
    /**
     * 解析URI的编码格式
     * decodeURIComponent() 函数可对 encodeURIComponent() 函数编码的 URI 进行解码。
     * @param {string} data
     * @returns {unresolved}
     */
    MediaBatchApprove.prototype._parseURIComponent = function (data) {
        data = decodeURIComponent(data);    //uri 进行解码
        // 匹配form表单生成的参数字符正则
        var reg = /([^=&\s]+)[=\s]*([^&\s]*)/g;
        // 返回的formdata数组
        var formdata = {};
        while (reg.exec(data)) {
            if(!formdata[RegExp.$1]){
                formdata[RegExp.$1] = RegExp.$2;
            }else if(formdata[RegExp.$1]){
                if(formdata[RegExp.$1] != 'object'){
                    formdata[RegExp.$1] = [formdata[RegExp.$1]];
                    
                }
                formdata[RegExp.$1].push(RegExp.$2);
            }
        }
        
        return formdata;
    };
    
    
    /**
     * 去除数组中的空值
     * @param {array} array
     * @returns {unresolved}
     */
    MediaBatchApprove.prototype._trimSpace =  function (array) {
        for (var i = 0; i < array.length; i++)
        {
            if (array[i] == "" || typeof (array[i]) == "undefined")
            {
                array.splice(i, 1);
                i = i - 1;

            }
        }
        return array;
    }

    
    //--------------------------------------------------------------------------
    //
    // public
    //
    //--------------------------------------------------------------------------
    /**
     * 初始上传组件，准备所有数据，也可以后面再补其它数据
     * @param {array} files             素材文件信息数据
     * @returns {void}  
     */
    MediaBatchApprove.prototype.init = function (mediaFiles) {
        mediaFiles = mediaFiles || [];
        
        var _self = this, mediaData, index = 0;
        mediaData = new MediaData(index++, mediaFiles);
        
        _self.medias.push(mediaData);
    };
    
    /**
     * 提交数据，已经提交的不再提交
     * @param {object} submit_common_params     设置上传公共参数
     * @param {boole} force                     强制提交默认为false
     * @returns {void}
     */
    MediaBatchApprove.prototype.submit = function(submit_common_params, force){
        force = !!force;
        this.submit_index = -1;
        this.config['submit_common_params'] = $.extend(
            this.config['submit_common_params'], 
            this._parseURIComponent(submit_common_params)
        );
        this.config['submit_force'] = force;
        this.__submitNext();
    };
    
    
    win.mediaapprove = win.mediaapprove || {};
    win.mediaapprove.MediaBatchApprove = MediaBatchApprove;
    
})(window, jQuery);