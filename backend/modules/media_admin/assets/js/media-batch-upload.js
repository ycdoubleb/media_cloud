/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function (win, $) {
    
    //================================================================================================
    //
    // MediaData class
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
        
        this.id = id;  
        this.file_id = data.id;                             //文件id
        this.name = data.name;                              //文件名
        this.cover_url = data.thumb_url;                    //文件缩略图
        this.url = data.url;                                //文件绝对路径
        this.duration = data.metadata.duration;             //文件时长
        this.size = data.size;                              //文件大小
        this.ext = data.ext;                                //文件后缀名
        this.oss_key = data.oss_key                         //文件oss_key
        this.md5 = data.md5                                 //文件MD5

        this.media_id = null;                               //媒体id
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
            Media: {
                file_id: this.file_id, 
                name: this.name, 
                cover_url: this.cover_url,
                url: this.url, 
                duration: this.duration, 
                size: this.size, 
                ext: this.ext,
                oss_key: this.oss_key,
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
        this.media_id = result ? dbdata.id : null;
        this.sentChangeEvent();
    };
    
    
    
    
    //================================================================================================
    //
    // MediaBatchUpload class
    //
    //================================================================================================
    /**
     * 媒体批量导入控制器
     * @param {type} config
     * @returns {media-batch-uploadL#7.MediaBatchUpload}
     */
    function MediaBatchUpload(config) {
        this.config = $.extend({
            //添加素材url     
            media_url: 'create',                    
            //已提交的强制提交
            submit_force: false,                    
            //提交公共参数，如 form
            submit_common_params: {},   
            //素材信息容器
            resultinfo: '.result-info',                      
            
        }, config);
        //dom
        this.resultinfo = $(this.config['resultinfo']);
        //model
        this.medias = [];           //素材信息数据
        //vars
        this.is_submiting = false;  //是否提交中
        this.submit_index = -1;     //当前提交索引
        this.completed_num = 0      //完成数量
    }
    
    /**
     * 视图 创建结果信息
     * @param {MediaData} mediaData
     * @returns {undefined}
     */
    MediaBatchUpload.prototype.__createResultInfo = function (mediaData) {
        var _self = this,
            max_num = _self.medias.length,
            completed_num = _self.completed_num;
            
        $progress = this.resultinfo.find('div.result-progress');
        $hint = this.resultinfo.find('p.result-hint');
        $table = this.resultinfo.find('table.result-table');

        if(mediaData.submit_result){
            _self.completed_num = ++completed_num;
        }else{
            $(Wskeee.StringUtil.renderDOM(_self.config['media_data_tr_dom'], mediaData)).appendTo($table.find('tbody'));
        }
        
        $progress.css({width: parseInt(_self.completed_num / max_num * 100) + '%'}).html(parseInt(_self.completed_num / max_num * 100) + '%');
        $hint.find('span.max_num').html(max_num);
        $hint.find('span.completed_num').html(_self.completed_num);
        $hint.find('span.lose_num').html(max_num - _self.completed_num);
    }
    
    //------------------------------------------------------
    // 提交数据
    //------------------------------------------------------
    /**
     * 提交下一个任务
     * @returns {void}
     */
    MediaBatchUpload.prototype.__submitNext = function () {
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
    MediaBatchUpload.prototype.__submitMediaData = function (index, force) {
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
                        //code 不为0即为失败
                        md.setSubmitResult(2, response.code == "0", response.msg, response.data);
                        
                    } catch (e) {
                        if (console) {
                            console.error(e);
                        }
                        md.setSubmitResult(2, false, '未知错误');
                    }
                    
                    $(_self).trigger('submitCompleted', md);         //发送单个素材上传完成
                    _self.__createResultInfo(md);
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
    MediaBatchUpload.prototype.__parseURIComponent = function (data) {
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
    MediaBatchUpload.prototype._trimSpace =  function (array) {
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

    /**
     * 通过ID查找MediaDdata
     * @param {string} id
     * @returns {VideoData}
     */
    MediaBatchUpload.prototype.__getMediadataById = function (id) {
        var target = null;
        $.each(this.medias, function (index, mediadata) {
            if (id == mediadata.file_id) {
                target = mediadata;
            }
        });
        return target;
    };
    
    
    //--------------------------------------------------------------------------
    //
    // public
    //
    //--------------------------------------------------------------------------
    /**
     * 初始上传组件，准备所有数据，也可以后面再补其它数据
     * @param {array} medias             素材文件信息数据
     * @returns {void}  
     */
    MediaBatchUpload.prototype.init = function (medias) {
        medias = medias || [];
        var _self = this, mediaData;
        //array to MediaData
         for(var index in medias){
            mediaData = new MediaData(Number(index) + 1, medias[index]);
            _self.medias.push(mediaData);
        }
    };
    
    /**
     * 添加 MediaData
     * @param {object} media
     * @returns {undefined}
     */
    MediaBatchUpload.prototype.addMediaData = function(media)
    {
        var _self = this, 
            mediaData,
            index = _self.submit_index;
    
        _self.submit_index = ++index;
        
        var md = _self.__getMediadataById(media.id);
        if(!md){
            mediaData = new MediaData(_self.submit_index + 1, media);
            _self.medias.push(mediaData);
        }    
    }
    
    /**
     * 删除 MediaData
     * @param {object} media
     * @returns {undefined}
     */
    MediaBatchUpload.prototype.delMediaData = function(media)
    {
        var _self = this, 
            mediaData;
    
        var md = _self.__getMediadataById(media.id);
        if(md){
            var index = _self.medias.indexOf(md)
            _self.medias.splice(index, 1);
        }
        console.log(_self.medias);
    }
    
    /**
     * 提交数据，已经提交的不再提交
     * @param {object} submit_common_params     设置上传公共参数
     * @param {boole} force                     强制提交默认为false
     * @returns {void}
     */
    MediaBatchUpload.prototype.submit = function(submit_common_params, force){
        force = !!force;
        this.submit_index = -1;
        this.config['submit_common_params'] = $.extend(
            this.config['submit_common_params'], 
            this.__parseURIComponent(submit_common_params)
        );
        this.config['submit_force'] = force;
        this.__submitNext();
    };
    
    
    win.mediaupload = win.mediaupload || {};
    win.mediaupload.MediaBatchUpload = MediaBatchUpload;
    
})(window, jQuery);