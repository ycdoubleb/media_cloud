<div class="chart-panel">
    <input type="checkbox" class="checkbox hidden" name="Media[id]" value="{%id%}" />
    <div class="chart-header">
        <a href="/media_library/default/view?id={%id%}" title="{%name%}" target="_blank">
            <img src="{%cover_img%}" width="100%" height="100%" />
        </a>
    </div>
    <div class="chart-body">
        <div class="tuip tuip-title">
            <span class="title single-clamp">{%name%}</span>
        </div>
        <div class="tuip single-clamp">{%tags%}</div>
    </div>
    <div class="chart-footer">
        <div class="tuip">
            <div class="pull-left glyphicon glyphicon-shopping-cart"></div>
            <div class="pull-right">
                <a href="/media_library/default/add-cart?id={%id%}" class="add-cart">加入购物车</a>&nbsp;
                <a href="/media_library/default/view?id={%id%}" class="buy-now">立即购买</a>
            </div>
        </div>
    </div>
</div>

