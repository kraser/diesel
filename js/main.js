jQuery(document).ready(function()
{
    if (typeof initFancy !== "undefined")
        initFancy;

    //$(document).on('click', "#submit")
    var container = $(".userBox");
    container.click(".userAction", auth);
});

function setQuantity(id, quantity, el)
{
    //var element = $("#count" + basketId);
    var value = parseInt(quantity);
//    if(value <= 0)
//    {
//        location.href="basket/del/" + id;
//    }

    $.ajax({
        type: 'post',
        url: '/Basket/edit',
        dataType: "json",
        data:
        {
            //id: id,
            quantity: value,
            basketId: id
        },
        success: function(reply)
        {
//            if (!reply.result) {
//                //alert("Извитите, что-то пошло не так :(");
//            }
//            else
//            {
//                element.attr('value', value);
//                element.val(value);
//                var regexp = /[^,0-9]+/;
//                var total = reply.totals.summ.replace("&nbsp;", "").replace(regexp, "");
//                $('#BasketInfo span').html(total);
//                $('#dTPrice' + basketId).html(reply.tprice);
//                $("#dSTCount").html(reply.totals.count);
//                $("#dSTPrice").html(reply.totals.summ);
//            }
            $('li.navbar-minicart').html(reply.html);
            if ( value <= 0)
                $(el).closest("tr").remove();

            $(el).closest("tr").find('td.product-subtotal span.amount').html(reply.basket.products[id].total);
        }
    });


}

function addToBasket(id, quantity)
{
    $.ajax(
    {
        type: 'post',
        url: '/Basket/add',
        dataType: "json",
        data:
        {
            'id': id,
            'format': 'inlist',
            'quantity': quantity
        },
        success: function(reply)
        {
//            if (!reply.result)
//            {
//                //alert("Извините, что-то пошло не так :(");
//            }
//            else
//            {
//
//                $('#buyButtonWrap' + id).html(reply.buybutton);
//
//                var imgSrc = reply.block.count ? basketImg.fullImg : basketImg.emptyImg;
//
//                $('#BasketImg img').attr("src", imgSrc);
//                var el = $('#BasketInfo span');
//                el.html(reply.block.summ);
//
//                $('#mobiel-basket-count').html(reply.basket.count);
//            }

            //$('#minicart-count-num').html(reply.basket.count);
            $('li.navbar-minicart').html(reply.html);
            alert("Товар добавлен в корзину");
        }
    });
}

function valToValue(el)
{
    var elem = $(el);
    elem.attr("value", elem.val());

}

/**
 * Comment
 */
function authorize(e)
{
    var el = $(this);
    //var name = e.data;
    e.stopPropagation();
    var type = el.attr('name');

    $.post("/user/" + type, {login: 'login', password: 'pwd'}, function(data)
    {
        if (data && data.indexOf("Выход") != -1)
        {
            $(".enter").html(data);
            $(".authBox").hide();
        }
        else
        {
            $(".authBox").hide();
        }

    });

}

function auth(e)
{
    var el = $(e.target);

    if (!el.hasClass("userAction"))
        return;
    //e.stopPropagation();
    var type = el.attr('name');
    if (type == 'registerAction' || type == "loginAction")
    {
        var button = $(".userBox button");
        button.html((type == "registerAction" ? "Регистрация" : "Вход"));
        var login = $("#log").val();
        var password = $("#pwd").val();
    }

    $.post("/user/" + type, {login: login, password: password}, function(data)
    {
        $(".userBox").html(data);

        $(".userBox input#log").focus();
        // обработка Enter
        $(".userBox input#log, .userBox input#pwd").keyup(function(event) {
            if (event.which == 13) {
                $("button.userAction").click();
            }
        });

        if (type == "logoutAction")
        {
            var url = window.location.href;
            if (url.indexOf("privateOffice") != -1)
            window.location = "/"
        }
    });
}

function objectFromXml(node) {
    var result = {};
    if (node.attributes) {
        for (var i = 0; i < node.attributes.length; ++i) {
            result[node.attributes.item(i).nodeName] = node.attributes.item(i).value;
        }
    }
    for (var child = node.firstChild; child; child = child.nextSibling) {
        if (child.nodeType == 1) { // ELEMENT_NODE
            if (child.attributes.length) {
                result[child.tagName] = objectFromXml(child);
            } else {
                result[child.tagName] = arrayFromXml(child);
            }
        }
    }
    return result;
}

/** <CF><E0><F0><F1><E8><F2> xml <E2> <EC><E0><F1><F1><E8><E2> <EE><E1><FA><E5><EA><F2><EE><E2>
 *
 * @param index <CA><EB><FE><F7><E5><E2><EE><E5> <EF><EE><EB><E5> <E2> <EE><E1><FA><E5><EA><F2><E0><F5>, <EF><EE> <EA><EE><F2><EE><F0><EE><EC><F3> <E8><ED><E4><E5><EA><F1><E8><F0><F3><E5><F2><F1><FF> <EC>
 <E0><F1><F1><E8><E2>
 * @returns     <CC><E0><F1><F1><E8><E2> <F0><E0><F1><EF><E0><F0><F1><E5><ED><ED><FB><F5> <EE><E1><FA><E5><EA><F2><EE><E2>
 */
function arrayFromXml(node, indexField) {
    var ar = node.childNodes;
    var array = new Array();
    for (var i = 0; i < ar.length; i++)
    {
        var el = ar.item(i);
        array.push(objectFromXml(el));
    }
    //var array = $A(node.childNodes).collect (objectFromXml);
    if (indexField && array) {
        array = mapFromArray(array, indexField);
    }
    return array;
}

/** <D1><EE><E7><E4><E0><B8><F2> <E0><F1><F1><EE><F6><E8><E0><F2><E8><E2><ED><FB><E9> <EC><E0><F1><F1><E8><E2> <EF><EE> <EA><EB><FE><F7><E5><E2><EE><EC><F3> <EF><EE><EB><FE>
 *
 * @param array         <CC><E0><F1><F1><E8><E2> <EE><E1><FA><E5><EA><F2><EE><E2>
 * @param indexField    <CD><E0><E7><E2><E0><ED><E8><E5> <EA><EB><FE><F7><E5><E2><EE><E3><EE> <EF><EE><EB><FF>
 */
function mapFromArray(array, indexField) {
    var indexed = {};
    var keys = array.pluck(indexField);
    for (var i in keys) {
        indexed[keys[i]] = array[i];
    }
    return indexed;
}

/**
 * <CA><EE><ED><E2><E5><F0><F2><E8><F0><F3><E5><F2> <F1><F2><F0><EE><EA><F3> <E2> XML
 * @param text XML-<F1><F2><F0><EE><EA><E0>
 * @return XML <E4><EE><EA><F3><EC><E5><ED><F2>
 */
function stringToXml(text)
{
    var doc;
    if (window.ActiveXObject)
    {
        doc = new ActiveXObject('Microsoft.XMLDOM');
        doc.async = 'false';
        doc.loadXML(text);
    }
    else
    {
        var parser = new DOMParser();
        doc = parser.parseFromString(text, 'text/xml');
    }
    return doc;
}

function getMousePosition(e)
{
	if (e.pageX || e.pageY)
    {
		var posX = e.pageX;
		var posY = e.pageY;
	}
    else if (e.clientX || e.clientY)
    {
		 posX = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
		 posY = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
	}
	return {x:posX, y:posY};
}

function getPosition(obj)
{
    if ( !obj )
        return;
    var left = 0;
    var top = 0;
    var width = parseInt(obj.offsetWidth);
    var height = parseInt(obj.offsetHeight);
    while(obj !== null)
    {
        left += !obj.style.left ? obj.offsetLeft : parseInt(obj.style.left);
        top += !obj.style.top ? obj.offsetTop : parseInt(obj.style.top);
        obj = obj.offsetParent;
    }
    return { x: left, y: top, w: width, h: height };
}