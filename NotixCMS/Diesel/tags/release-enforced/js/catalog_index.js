initCategory = function() 
{
    $("#indexCatalogList .sub_catalog_list.level_2").each(function()
    {
        var li = $(this).parents(".sub_catalog_item.level_1");
        var li_0 = $(this).parents(".sub_catalog_item.level_0");
        li.addClass("sub_catalog_subhide").find("> span").after(
            $("<span class=\"open_sub_list\"><b/></span>").click(function()
            {
                var li_2 = $(".sub_catalog_list.level_2", li);
                var pos = li_0.position();
                var s = li.data("open");
                var heights = [];
                $(".sub_catalog_item.level_0").each(function() 
                {
                    var p = $(this).position();
                    if (pos.left == p.left && pos.top < p.top) 
                    {
                        var n = p.top - li_2.outerHeight(true) * (s ? 1 : -1);
                        $(this).css({top: n + "px"});
                        p.top = n;
                    }
                    heights.push(p.top + $(this).height());
                });
                var max_height = 0;
                for(var h in heights) 
                {
                    if (heights[h] > max_height)
                    {
                        max_height = heights[h];
                    }
                }
                $("#indexCatalogList").height(max_height);
                li.toggleClass("sub_catalog_subhide").data("open", !s);
            }));
    });
    $("#indexCatalogList").fluid_columns();
    
    $(".unzip .open_all").click(function()
    {
        $("#indexCatalogList li.sub_catalog_subhide > .open_sub_list").trigger('click');
    });
    $(".unzip .close_all").click(function() 
    {
        $("#indexCatalogList li:not(.sub_catalog_subhide) > .open_sub_list").trigger('click');
    });
    
    //$(".unzip .open_all").trigger('click');
}