$("#officeTabs").tabs();
$("#officeTabs input[type=\'button\']").button();
$("img[name=\'minifier\']").click(function(){
    var el = $(this);
    var orderId = el.parent().parent().attr('id');
    if(el.hasClass("switchDown"))
    {
        $("#order_" + orderId).show();
        el.removeClass("switchDown");
        el.addClass("switchUp");
        el.attr('title', 'Свернуть заказ');

    }
    else
    {
        $("#order_" + orderId).hide();
        el.removeClass("switchUp");
        el.addClass("switchDown");
        el.attr('title', 'Развернуть заказ');
    }
    var s;
});
$("#orders table td[name=\'address\'], #orders table td[name=\'phone\']").click(updateData);

function updateData()
{
    var el= $(this);
    var input = el.find("input");
    input.show().change(function()
    {
        var val = input.val();
        var span = el.find("span");
        span.html(val);
        span.show();
        input.attr("value", val).hide();
    }).blur(function()
    {
        var val = input.val();
        var span = el.find("span");
        span.html(val);
        span.show();
        input.attr("value", val).hide();
    });

    el.find("span").hide();

}

$("#addNew").click(function()
{
    var inputs = $("#basketOrder input[type=\'radio\']:checked, #basketOrder input[type=\'text\']");
    var data = {};
    inputs.each(function()
    {
        var name = $(this).attr("name");
        if(name.indexOf("payments") != -1)
            name = "payments";

        var value = $(this).val();
        data[name] = value;
    });

    data.shopId = $("#deliveryType_1 select[name=\'shopId\'] option:selected").val();

    $.ajax(
    {
        url: '/privateOffice/order/create/',
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(json)
        {
            if(json['error'])
            {
                if(json.error.address)
                    $('#basketOrder').next('span.error').html(json.error.address).show();
                if(json.error.phone)
                    $('#basketOrder').next('span.error').html(json.error.phone).show();
            }
            else if(json['redirect'])
            {
                window.location = json['redirect'];
            }
        }
    });
});

$("#orderList input[type=\'button\']").click(function()
{
    var el = $(this);
    var orderId = el.parent().parent().attr('id');
    var inputs = $("#basketOrder input[type=\'radio\']:checked, #order_" + orderId + " input[type=\'text\'], #order_" + orderId + " input[type=\'hidden\'], #delivery input[type=\'radio\']:checked");
    var data = {};
    inputs.each(function()
    {
        var name = $(this).attr("name");
        var value = $(this).val();
        if(name.indexOf("payments") != -1)
        {
            name = "payments";
        }
        if(name.indexOf("address") != -1)
        {
            var cell = $("#basketOrder input[name=\'address\']");
            var basketAddress = cell.val();
            value = basketAddress ? basketAddress : value;
        }

        data[name] = value;
    });

    data.shopId = $("#deliveryType_1 select[name=\'shopId\'] option:selected").val();

    $.ajax(
    {
        url: '/privateOffice/order/modify/',
        type: 'post',
        data: data,
        dataType: 'json',
        success: function(json)
        {
            if(json['error'])
            {
                if(json.error.address)
                    $('#basketOrder').next('span.error').html(json.error.address).show();
                if(json.error.phone)
                    $('#basketOrder').next('span.error').html(json.error.phone).show();
            }
            else if(json['redirect'])
            {
                window.location = json['redirect'];
            }
        }
    });
});

$("#delivery input[type=\'radio\']").click(function()
{
    var id = $(this).val();
    $("#delivery div[name=\'description\']").hide();
    $("#deliveryType_" + id).show();
});

$("#deliveryType_1").ready(function()
{
    if (typeof descr === "undefined")
        return;

    var val = $("#deliveryType_1 select[name=\'shopId\'] option:selected").val();
    $("#inform").html(descr[val]);
});

$("#deliveryType_1 select[name=\'shopId\']").change(function()
{
    if (typeof descr === "undefined")
        return;

    var val = $("#deliveryType_1 select[name=\'shopId\'] option:selected").val();
    $("#inform").html(descr[val]);
});