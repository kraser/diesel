<div class="checkout">
    <div id="checkout">
      <div class="ui-widget-header checkout-heading"><?php echo $headAuthOption; ?></div>
      <div class="checkout-content" <?php echo (!$logged ? "style='display:block;'" : ""); ?>
           <div id='authOption'>
          <?php echo $authOption; ?>
               </div>
          <div id='remind'>
              <span class="required">*</span> Введите E-mail указанный при регистрации:<br>
  <input type="text" id='mail-remind' class="large-field" style='width: 500px;' value="" name="mail">
  <span class='error' style="height: 30px;"></span>
  <input id='button-remind' type='button' value='Отправить'>
          </div>
      </div>
    </div>
    <div id="payment-address">
      <div class="ui-widget-header checkout-heading"><?php echo $headPayInfo; ?></div>
      <div class="checkout-content" <?php echo ($logged ? "style='display:block;'" : ""); ?>><?php echo $payInfo; ?></div>
    </div>
    <!--<div id="shipping-address">
      <div class="ui-widget-header checkout-heading"><span>Head3</span></div>
      <div class="checkout-content">Content 3<input type="button" onclick="slide(this);"></div>
    </div>
    <div id="order-info">
      <div class="ui-widget-header checkout-heading">Head4</div>
      <div class="checkout-content">Conten 4<input type="button" onclick="slide(this);"></div>
    </div>-->
</div>
<script type="text/javascript">



function remind()
{
    $("#authOption").hide();
    $("#remind").show();
    //var header = $("#checkout .checkout-heading").html();
    var head = $("#checkout .checkout-heading span");
    head.html("Восстановление пароля");
}

$('#button-remind').button();
$('#button-account').button();
$('#button-login').button();

    function slide(el) {
    $(el).parent().slideUp("slow");
    //$(el).parent().parent().find('.checkout-content').slideDown('slow');

}

$('#button-remind').click(function()
{
    $.ajax(
    {
        url: '/user/remind',
        type: 'post',
        data: $('#mail-remind'),
        dataType: 'json',
        beforeSend: function()
        {
            //$('#button-login').attr('disabled', true);
        },
        complete: function()
        {
            //$('#button-login').attr('disabled', false);
        },
        success: function(json)
        {
            if (json['error'])
            {
                if (json['error']['mail'])
                    {
                    //$('#remind input[name=\'mail\']').after('<span class="error">' + json['error']['mail'] + '</span>');
                    $('#remind input[name=\'mail\']').next('span').html(json['error']['mail']);
                    }
            }
            else
            {
                alert(json.report);
                $("#checkout .checkout-heading span").html("Шаг 1: Способ оформления заказа<a style='display: none;'>Изменить »</a>");
                $("#authOption").show();
                $("#remind").hide();

            }
        }
    });
});

$('.checkout-heading a').hide();

$('.checkout-heading a').click( function()
{
	$('.checkout-content').slideUp('slow');

	$(this).parent().parent().find('.checkout-content').slideDown('slow');
    $("#remind").hide();
});


$('#button-account').click(function()
{
    $.ajax(
    {
        url: '/privateOffice/registration/' + $('input[name=\'account\']:checked').attr('value'),
        dataType: 'html',
        beforeSend: function()
        {
            $('#button-account').attr('disabled', true);
            //$('#button-account').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function()
        {
            $('#button-account').attr('disabled', false);
            //$('.wait').remove();
        },
        success: function(html)
        {
            $('#payment-address .checkout-content').html(html);
            //$("#payment-address .rightCol").hide()
            $('#checkout .checkout-content').slideUp('slow');
            $('#payment-address .checkout-content').slideDown('slow');
            $('.checkout-heading a').hide();
            $('#checkout .checkout-heading a').show();
            $('#payment-address .error').hide();
            $("#payment-address .button").button();
            $("#companyFlag").change(function()
            {
                $("#payment-address .rightCol").toggle()
            });
        },
        error: function(xhr, ajaxOptions, thrownError)
        {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

$('#button-login').click( function()
{
    var userData = {};
    var valid = true;
    $('#checkout #login :input').each(function()
    {
        var el = $(this);
        var name = el.attr('name');
        var field = el.val();
        if(name == "login" || name == "password")
            userData[name] =  field;

    });

    var regex = /[^A-Za-z0-9]/;
    var valid = !userData.login.match(regex) && userData.login.length && !userData.password.match(regex) && userData.password.length;
    if(!valid)
    {
        alert("Недопустимые символы в поле Логин/пароль.\n Логин/пароль может состоять только из цифр, латинских букв, знаков тире и подчеркивания");
        return;
    }
    $.ajax(
    {
        url: '/privateOffice/login',
        type: 'post',
        data: $('#checkout #login :input'),
        dataType: 'json',
        beforeSend: function()
        {
            $('#button-login').attr('disabled', true);
        },
        complete: function()
        {
            $('#button-login').attr('disabled', false);
        },
        success: function(json)
        {
            var response = json;
            if(response.error)
                $("#login .loginError").html("Неверный логин или пароль").show();
            else
            {
            var html = Base64.decode(response.html);
            $("#login .loginError").hide();
            $('#checkout .checkout-content').slideUp('slow');
            $('#payment-address .checkout-content').html(html).slideDown('slow');
            //$('.checkout-heading a').hide();
            $('#checkout .checkout-heading a').show();
            $("#payment-address .button").button();
    /*
			$('.warning, .error').remove();

			if (json['redirect']) {
				location = json['redirect'];
			} else if (json['error']) {
				$('#checkout .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');

				$('.warning').fadeIn('slow');
			}*/
            }
        },
        error: function(xhr, ajaxOptions, thrownError)
        {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});

function register()
{
    var data = $('#recaptcha_response_field, #recaptcha_challenge_field, #payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select');
    var login = $("#payment-address input[name='login']").val();
    var password = $("#payment-address input[name='password']").val();
    var regex = /[^-_A-Za-z0-9]/;
    var valid = !login.match(regex) && login.length && !password.match(regex) && password.length;
    if(!valid)
    {
        alert("Недопустимые символы в поле Логин/пароль.\nЛогин/пароль может состоять только из цифр, латинских букв, знаков тире и подчеркивания");
        return;
    }
    $.ajax(
    {
        url: '/privateOffice/registration/validate',
        type: 'post',
        data: data,
        dataType: 'json',
        beforeSend: function()
        {
            $('#button-register').attr('disabled', true);
            //$('#button-register').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function()
        {
            $('#button-register').attr('disabled', false);
            //$('.wait').remove();
        },
        success: function(json)
        {
            $('.warning, .error').remove();

            if (json['error'])
            {
                if (json['error']['firstname'])
                {
                    $('#payment-address input[name=\'firstname\']').after('<span class="error">' + json['error']['firstname'] + '</span>');
                }

                if (json['error']['lastname'])
                {
                    $('#payment-address input[name=\'lastname\']').after('<span class="error">' + json['error']['lastname'] + '</span>');
                }

                if (json['error']['email'])
                {
                    $('#payment-address input[name=\'email\']').after('<span class="error">' + json['error']['email'] + '</span>');
                }

                if (json['error']['telephone'])
                {
                    $('#payment-address input[name=\'telephone\']').after('<span class="error">' + json['error']['telephone'] + '</span>');
                }

                if (json['error']['login'])
                {
                    $('#payment-address input[name=\'login\']').after('<span class="error">' + json['error']['login'] + '</span>');
                }

                if (json['error']['address_1'])
                {
                    $('#payment-address input[name=\'address_1\']').after('<span class="error">' + json['error']['address_1'] + '</span>');
                }

                if (json['error']['city'])
                {
                    $('#payment-address input[name=\'city\']').after('<span class="error">' + json['error']['city'] + '</span>');
                }

                if (json['error']['password'])
                {
                    $('#payment-address input[name=\'password\']').after('<span class="error">' + json['error']['password'] + '</span>');
                }

                if (json['error']['confirm'])
                {
                    $('#payment-address input[name=\'confirm\']').after('<span class="error">' + json['error']['confirm'] + '</span>');
                }

                if (json['error']['zone_id'])
                {
                    $('#payment-address select[name=\'zone_id\']').after('<span class="error">' + json['error']['zone_id'] + '</span>');
                }

                if (json['error']['captcha'])
                {
                    $('#authCaptcha').after('<span class="error">' + json['error']['captcha'] + '</span>');
                }


            }
            else
            {
                location = '/privateOffice/order';
            }
        },
        error: function(xhr, ajaxOptions, thrownError)
        {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}

function update()
{
    $.ajax(
    {
        url: '/privateOffice/registration/update',
        type: 'post',
        data: $('#payment-address input[type=\'text\'], #payment-address input[type=\'password\'], #payment-address input[type=\'checkbox\']:checked, #payment-address input[type=\'radio\']:checked, #payment-address input[type=\'hidden\'], #payment-address select'),
        dataType: 'json',
        beforeSend: function()
        {
            $('#button-confirm').attr('disabled', true);
            //$('#button-register').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
        },
        complete: function()
        {
            $('#button-confirm').attr('disabled', false);
            //$('.wait').remove();
        },
        success: function(json)
        {
            if (json['error'])
            {
                var msg = "";
                if (json['error']['mail'])
                {
                    msg += json['error']['mail'] + "\n";
                }

                if (json['error']['phone'])
                {
                    msg += json['error']['phone'] + "\n";
                }

                alert("Возникли следующие ошибки:\n" + msg);
            }
            else
            {
                window.location = '/privateOffice/order';
            }
        },
        error: function(xhr, ajaxOptions, thrownError)
        {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
}
</script>