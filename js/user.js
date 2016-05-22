jQuery(document).ready(function()
{
    $("#userInfo").click(function()
    {
        window.location = "/privateOffice"
    });
})

function toggleAction(el)
{
    var toShow = $(el).attr("href");
    $(toShow).show();
    $("#userInfo div").each(function(i, div){
        var id = "#" + $(div).attr('id');
        if( id != "#" && id != toShow)
            $(div).hide();
    });
    
    $("#actionChoice a").each(function(i, a){
       $(a).removeClass("selectedChoice"); 
    });
    $(el).addClass("selectedChoice");
}

