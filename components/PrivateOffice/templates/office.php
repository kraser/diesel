<div id="officeTabs">
    <ul>
        <!--<li><a href='#profile'>Профиль</a></li>-->
        <li><a href='#orders'>Заказы</a></li>
        <!--<li><a href='#payments'>Платежи</a></li>-->
    </ul>
    <!--<div id='profile'>
        <?php echo Starter::app ()->getModule("User")->getShortProfile(); ?>
    </div>-->
    <div id='orders'>
        <?php echo Starter::app ()->getModule("PrivateOffice")->orders(); ?>
    </div>
    <!--<div id='payments'>
        История платежей
    </div>-->
</div>
<script type="text/javascript">

    $("#officeTabs").tabs();

    $("#officeTabs input[type=\'button\']").button();
    /*
    $("#addNew").click(function()
    {
        $("#basketOrderInfo").show();
    });
    */
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
    //$("#basketAddress").click(updateAddress)
/*
    $("#basketAddress").click(function()
    {
        $("#basketAddress input").show().change(function()
        {
            var val = $("#basketAddress input").val();
            $("#basketAddress span").html(val);
            $("#basketAddress span").show();
            $("#basketAddress input").attr("value", val).hide();
        });
        $("#basketAddress span").hide();
    });
*/



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
                    $('#basketOrder').next('span.error').html(json.error.address).show();
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

        var inputs = $("#order_" + orderId + " input[type=\'radio\']:checked, #order_" + orderId + " input[type=\'text\'], #order_" + orderId + " input[type=\'hidden\']");
        var data = {};
        inputs.each(function()
        {
            var name = $(this).attr("name");
            if(name.indexOf("payments") != -1)
                name = "payments";

            var value = $(this).val();
            data[name] = value;
        });

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
                    //@todo Error processing
                }
                else if(json['redirect'])
                {
                    window.location = json['redirect'];
                }
            }
        });
    });
</script>