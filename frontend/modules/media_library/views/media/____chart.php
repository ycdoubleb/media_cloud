<div class="chart-panel">
    <div class="checkdiv"><input type="checkbox" class="checkbox hidden" name="selection[]" value="{%id%}"/></div>
    <div class="chart-header">
        <a href="/media_library/media/view?id={%id%}" title="{%name%}" target="_blank">
            <img src="{%cover_img%}" width="100%" height="100%" />
        </a>
    </div>
    <div class="chart-body">
        <div class="tuip tuip-title">
            <div class="title single-clamp">{%name%}</div>
        </div>
        <div class="tuip single-clamp">{%tags%}</div>
    </div>
    <div class="chart-footer">
        <div class="tuip">
            <div class="pull-left {%icon%}" title="{%type_name%}"></div>
            <div class="pull-right">
                <a href="javascript:;" data-url="/media_library/media/add-carts" data-id="{%id%}" class="add-cart">加入购物车</a>&nbsp;
                <a href="/media_library/media/checking-order?id={%id%}" class="buy-now">立即购买</a>
            </div>
        </div>
    </div>
</div>

