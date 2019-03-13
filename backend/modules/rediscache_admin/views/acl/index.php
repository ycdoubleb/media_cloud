<?php
$this->title = Yii::t('app', '{Visit}{Path}', [
            'Visit' => Yii::t('app', 'Visit'), 'Path' => Yii::t('app', 'Path')
        ]);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rediscache_admin-default-index">
    <div style="width:50%;display: inline-block;margin-right: 10px;">
        <input id="key-input" list="browsers" name="browser" class="form-control" />
        <datalist id="browsers">
            <option value="Internet Explorer">
            <option value="Firefox">
            <option value="Chrome">
            <option value="Opera">
            <option value="Safari">
        </datalist>
        <select multiple="multiple" class="form-control keylist" size="30" onchange="selectKey($(this).val()[0])">
        </select>
    </div>
    <div style="width:49%;display: inline-block;float: right;">
        <table class="key-info">
            <tr class="name">
                <th>Key</th>
                <td>mediacloud:acl:dirty</td>
            </tr>
            <tr class="type">
                <th>Type</th>
                <td>SET</td>
            </tr>
            <tr class="ttl">
                <th>TTL</th>
                <td>-1</td>
            </tr>
        </table>
        <table class="key-detail">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>
</div>
<script>
    window.onload = function(){
        searchKey();
    }
    
    /**
     * 搜索key
     */
    function searchKey(key){
        if(key == null || key == ""){
            key = "*";
        }
        $.get('acl/search-key',{key:key},function(r){
            buildKeyList(r.data.keys);
        });
    }
    /**
     * 构建键列表
     * @param {array} kyes
     * @returns {void}
     */
    function buildKeyList(keys){
        $('.keylist').empty();
        $.each(keys,function(index,item){
            $('.keylist').append($(Wskeee.StringUtil.renderDOM('<option value="{%label%}">{%label%}</option>',{label:item})));
        });
    }
    
    /**
     * 选择显示当前key
     * @param {string} key
     * @returns {void}
     */
    function selectKey(key){
        $.get('acl/get-value',{key:key},function(r){
            console.log(r);
            reflashKeyDetail(r.data);
        });
    }
    
    function reflashKeyDetail(data){
        $('.key-info tr[name] > td');
        console.log($('.key-info tr[name] > td'));
    }
</script>
