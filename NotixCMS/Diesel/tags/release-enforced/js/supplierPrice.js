var jQ = jQuery.noConflict();
var lastSel;
var lastEditRow;

/**
 * Инициализация дерева категорий
 */
function initTrees()
{
    jQ("#tree").showTree(
    {
        closeFolders: true
    });
}

jQ(document).ready(function()
{
    initTrees();
    initPriceGrid();

});

jQ(document).keydown(function(e){
    if(isEnterPressed(e))
    {
        if(jQ('#search').is(':focus'))
            jQ("#searchButton").click();
    }
})

$(document).observe('click', function(event)
{
    var cellsId = ['filterParamsString', 'filterSettings', 'searchSettings', 'searchParamsString'];
    var target = $(Event.element(event));
    var parents = target.ancestors().pluck('id');
    parents.unshift(target.id);
    var outside = !parents.any(function(id){return cellsId.indexOf(id) != -1});

    if (outside)
    {
        $('filterSettings').hide();
        $('searchSettings').hide();
    }
});

/**
 * Установка параметров Grid для загрузки и отображения позиций
 */
function displayItems(event)
{
    event.stopPropagation();
    var branch = jQ(this);
    jQ('#tree').find('span.ui-state-highlight').each(function()
    {
       jQ(this).removeClass('ui-state-highlight');
    });
    branch.addClass('ui-state-highlight');

	var node = branch.closest('li');
	var categoryId = node.attr('id');
	var id = new Array();

	if(categoryId != -1)
		id.push(categoryId);

	if(categoryId != 0)
	{
		var nodes = node.find("li");
		nodes.each(function()
		{
			var currentId = jQ(this).attr('id');
			if(currentId != -1)
				id.push(currentId);
		});
		categoryId = id.join(',');
	}

    $('search').value = '';
    var supplierId = location.search.sub('?', '', 1).toQueryParams().supplierId;
    var table = jQ('#itemsTable');
    var storeSet = $('setStore').checked ? '1' : '0';
    var params =
    {
        datatype: 'xml',
        url: "service/price/WideSupplierItemManager.php?f=getItems&categoryId=" + categoryId + "&supplierId=" + supplierId + "&store=" + storeSet
    };
    table.clearGridData().setGridParam(params).trigger("reloadGrid");
    Element.scrollTo($('gbox_itemsTable'));
}

/**
 * Очистка (обнуление наличия) прайса поставщика
 */
function clearSupplierPrice (supplierId)
{
    var operation =
    {
        header: "Очитка прайса",
        text: "Будут поставлены в отсутствие все позиции прайса поставщика",
        callback: "clearPrice",
        supplierId: supplierId
    }
    actAndInform(operation);
}

/**
 * Удаление неиспользуемых позиций прайса поставщика
 */
function deleteUnusedItems (supplierId)
{
    var operation =
    {
        header: "Удаление неиспользуемых позиций",
        text: "Будут удалены все неиспользуемые позиции",
        callback: "deleteUnusedItems",
        supplierId: supplierId
    }
    actAndInform(operation);
}

/**
 * Удаление категорий поставщика не имеющих позиций
 */
function clearSupplierCategories(supplierId)
{
    var operation =
    {
        header: "Удаление категорий",
        text: "Будут удалены все категории не имеющие позиций",
        callback: "clearCategories",
        supplierId: supplierId
    }
    actAndInform(operation);
}

/**
 * Удаление дублированных позиций
 */
function deleteDoubleItems(supplierId)
{
    var operation =
    {
        header: "Удаление дублей",
        text: "Будут удалены все дублированные позиции",
        callback: "deleteDoubleItems",
        supplierId: supplierId
    }
    actAndInform(operation);
}

/**
 * Инициализация заголовка Grid
 */
function initPriceGrid()
{
    var supplierId = location.search.sub('?', '', 1).toQueryParams().supplierId;
    var caption;
    jQ.get('service/price/parseReport/ParseReportManager.php',
    {
        f: "find",
        supplierId: supplierId,
        last: 1
    },
    function(data)
    {
        var report = objectFromXml(data).root.first();
        if(report)
            caption = "<span style='margin-right: 15px;'>" + report.supplier.code + " последнее обновление " + report.parseTime + " " + report.manager.name + "</span>";
        else
            caption = "<span style='margin-right: 15px;'>Записанных отчетов нет</span>";
        caption += "<a id='clearPrice' href='javascript:void(clearSupplierPrice(" + supplierId + "))' title='Очистить прайс поставщика (поставить полное отсутствие)'>Очистить прайс-лист</a>";
        caption += "<a id='deleteUnused' href='javascript:void(deleteUnusedItems(" + supplierId + "))' title='Удалить неиспользуемые позиции с непроставленными соответствиями'>Удалить неиспользуемые</a>";
        caption += "<button id='clearDoubles' onclick='deleteDoubleItems(" + supplierId + ");' title='Удалить дублированные позиции'>Удалить дубли</button>";
        caption += "<button id='clearCategories' onclick='clearSupplierCategories(" + supplierId + ");' title='Удалить категории поставщика в которых нет позиций'>Очистить категории</button>";

        caption += "<table style='font-size: 10pt;'><tr><td>";
        caption += "<button id='searchButton'>Поиск</button><input type='text' id='search' size='60'></td><td>";
        caption += "<span style='position: relative;'><a id='searchParamsString' class='search_link' href='javascript:void(toggleSettingsBlock(\"searchSettings\"));'>Наименование поставщика</a>";
        caption += "<div name='settingsBlock' id='searchSettings' style='padding:5px;width: 400px; z-index: 1;position: absolute; background-color: #FFFF9C;display: none;'>"
            + "<input id='bySupName' checked type='checkbox' checked onchange='searchSettings()' value='Наименование поставщика' name='searchParams'><label for='bySupName'>Искать по наименованию поставщика</label><br>"
            + "<input id='byCode' type='checkbox' onchange='searchSettings()' value='Код поставщика' name='searchParams'><label for='byCode'>Искать по коду поставщика</label><br>"
            + "<input id='byRefName' type='checkbox' onchange='searchSettings()' value='Наименование справочника' name='searchParams'><label for='byRefName'>Искать по наименованию в справочнике</label><br>"
            + "</div></span>";
        caption += "</td></tr><tr><td colspan='2'>";
        caption += "<div><a id='filterParamsString' class='search_link' href='javascript:void(toggleSettingsBlock(\"filterSettings\"));'>Показывать позиции в наличии</a>";
        caption += "<div name='settingsBlock' id='filterSettings' style='padding:5px;width: 400px; z-index: 1;position: absolute; background-color: #FFFF9C;display: none;'>"
            + "<input id='setStore' checked type='checkbox' onchange='filterSettings()'><label for='setStore'>Показывать только позиции в наличии</label></div></div>";
        caption += "</td></tr></table>";
        caption += "<div id='priceDialog'><p id='message'></p></div>";

        renderPriceGrid(caption);
    });
}

/**
 * Рендеринг Grid
 */
function renderPriceGrid(caption)
{
    jQ('#itemsTable').jqGrid(
    {
        datatype: 'local',
        caption: caption,
        autowidth: true,
        height: '100%',
        colNames:['','Код','', 'Наименование позиции', 'Цена прайса', 'Валюта' , 'Цена $ + %', 'Цена Руб.', 'М', 'Себестоимость', 'Наличие','Транзит','','Гарантия', 'Дата', '', '','','','','','','','',],
        colModel :
        [
            {
                name: 'id',
                index: 'id',
                width:60,
                fixed:true,
                sortable:false,
                resize:false,
                formatter:'actions',
                formatoptions:
                {
                    keys:true,
                    editbutton : true,
                    delbutton : false,
                    editformbutton: false,
                    onEdit : null,
                    onSuccess: null,
                    afterSave:refreshRow,
                    onError: null,
                    afterRestore: null,
                    extraparam: {oper:'edit'},
                    url: "service/price/WideSupplierItemManager.php?f=saveItem",
                    delOptions: {},
                    editOptions : {}
                },
                xmlmap: 'id'
            },
            {name:'code', index:'code', width: '25px', xmlmap: "[code]"},
            {name:'status', index:'status', width: '10px', align: 'center', title: false, formatter: statusFormat},
            {name:'originalName', index:'originalName', editable: true, xmlmap:"[originalName]"},
            {name:'priceValue', index:'priceValue', width: '25px', align: 'center', editable: true, formatter:priceFormat, edittype:'custom', editoptions:{custom_element: priceElem, custom_value:priceValue}},
            {name:'valuta', index:'valuta', width: '25px', align: 'center', editable: false, xmlmap:"[originalValuta]"},
            {name:'priceUsd', index:'priceUsd', width: '25px', align: 'center', editable: false, xmlmap:"[priceUsd]"},
            {name:'priceRur', index:'priceRur', width: '25px', align: 'center', editable: false, xmlmap:"[priceRur]"},
            {name:'modifiers', index:'modifiers', width: '20px', align: 'center', title: false, editable: false, formatter: modifiersFormat},
            {name:'cost', index:'cost', width: '25px', align: 'center', editable: false, formatter:costFormat, xmlmap:"[cost]"},
            {name:'store', index:'store', width: '25px', align: 'center', editable: true, xmlmap:"[store]"},
            {name:'transit', index:'transit', width: '25px', align: 'center', editable: true, xmlmap:"[transit]"},
            {name:'warranty', index:'warranty', hidden:true, xmlmap:"[warranty]"},
            {name:'displayWarranty', index:'displayWarranty', width: '25px', align: 'center', editable: true, label: 'Гарантия', title: false, formatter:warrantyFormat, edittype:'custom', editoptions:{custom_element: warrantyElem, custom_value:warrantyValue}},
            {name:'dateUpdate', index:'dateUpdate', width: '25px', align: 'center', xmlmap:"[dateUpdate]"},
            {name:'supplierCategory', index:'supplierCategory', hidden:true, xmlmap:"[supplierCategory]"},
            {name:'originalWarranty', index:'originalWarranty', hidden: true, xmlmap:'[originalWarranty]'},
            {name:'referenceCategory', index:'referenceCategory', hidden:true, xmlmap:'[referenceCategory]'},
            {name:'referenceName', index: 'referenceName', hidden: true, xmlmap:'[referenceName]'},
            {name:'referenceUser', index:'referenceUser',hidden: true, xmlmap:'[referenceUser]'},
            {name:'referenceDate', index:'referenceDate',hidden: true, xmlmap:'[referenceDate]'},
            {name:'priceStatus',index:'priceStatus',hidden: true,xmlmap:'[priceStatus]'},
            {name:'available',index:'available',hidden: true,xmlmap:'[available]'},
            {name:'referenceId',index:'referenceId',hidden: true,xmlmap:'[referenceId]'}
        ],
        onCellSelect: function(rowid, iCol, cellcontent, e)
        {
            if(iCol != 0)
                selectRow.call(this, rowid);
        },
        gridComplete : function()
        {
            jQ('#itemsTable .jqgrow').each(function(n, row)
            {
                jQ(row).addClass( ( 0 == n % 2 ) ? 'even' : 'odd' );
            })
        },

        loadComplete : function(data)
        {
            if(data.documentElement && data.documentElement.childNodes.length == 0 )
            {
                var target = jQ('#itemsTable').find('tr');
                jQ('<tr><td colspan="9" align="center"><div class="warning" style="margin:1em">Ничего не найдено</div></td></tr>').insertAfter(target);
            }
        },
        grouping:true,
        groupingView :
        {
            groupField : ['supplierCategory'],
            groupColumnShow : [false],
            plusicon: 'ui-icon-plus',
            minusicon: 'ui-icon-minus'
        },
        xmlReader:
        {
            root:'root',
            row:'WidePriceItem',
            repeatitems: false},
        rowNum: 10000,
        pager: '#itemsPager'
    });
    jQ( "#clearPrice" ).button();
    jQ( "#clearCategories" ).button();
    jQ( "#clearDoubles" ).button();
    jQ( "#deleteUnused" ).button();
    jQ( "#searchButton" ).button().click(searchItems);
    jQ( "#priceDialog" ).dialog(
    {
        autoOpen: false,
        modal: true
    });
}

function selectRow(id)
		{
            if(id && id != lastSel)
			{
				jQ('#itemsTable').restoreRow(lastSel);
				lastSel=id;
			}

			if(!jQ('#info'+id).attr('id'))
            {
                new Ajax.Request("service/price/WideSupplierItemManager.php",
                {
                    parameters:
                    {
                        f: 'getWideItem',
                        id: id
                    },
                    onComplete: function(transport)
                    {
                        if (parseError (transport))
                            return;

                        var object = objectFromXml(transport.responseXML.documentElement);
                        var tabsHtml = "<tr id = 'info" + id + "'><td colspan='13' style='padding:3px;' class='advancedInfo'>";
                        tabsHtml += "<div id='tabs" + id + "' class='tab" + id + "'>" + id + " " + object.originalName;
                        tabsHtml += "<ul>";
                        //tabsHtml += "<li name='referenceItem_" + id + "'><a href='#referenceItem_" + id + "'>Соответствие</a></li>";
                        tabsHtml += "<li name='priceItem_" + id + "'><a href='#priceItem_" + id + "'>Цены</a></li>";
                        //tabsHtml += "<li name='infoItem_" + id + "'><a href='#infoItem_" + id + "'>Ещё что-то...</a></li>";
                        tabsHtml += "</ul>";

                        //tabsHtml += "<div id='referenceItem_" + id + "'>" + object.referenceId + " " + object.referenceName + "</div>";
                        tabsHtml += "<div id='priceItem_" + id + "'>" + renderPrices(object) + "</div>";
                        //tabsHtml += "<div id='infoItem_" + id + "'>Здесь ещё какая-то информация о позиции</div>";
                        tabsHtml += "</div>";

                        tabsHtml += "</td></tr>";
                        jQ(tabsHtml).insertAfter("#"+id);
                        jQ("#tabs" + id).tabs();
                        var expDate = jQ("#expDate" + id).attr('value');
                        jQ("#expDate" + id).datepicker({ dateFormat: "yy-mm-dd", defaultDate: expDate });
                    }
                });
            }
            else
            {
                jQ('.progress').hide();
                jQ('#info'+id).remove();
            }
            lastSel = id;
        }

/**
 * Форматирование гарантии
 */
function warrantyFormat(cellvalue, options, rowObject)
{

    var data = cellvalue ? objectFromXml(cellvalue) : objectFromXml(rowObject);
    var html = '';
    if(data.warranty || data.originalWarranty)
    {
        html = (data.warranty) ? data.warranty : " - ";
        html += "&nbsp;/&nbsp;";
        html += data.originalWarranty ? "<span style='color:#888; white-space:nowrap;'>" + data.originalWarranty + "</span>" : ' - ';
    }
    else
    {
        if(cellvalue && cellvalue != " - ")
            html = cellvalue;
        else
            html = " - &nbsp;/&nbsp;<span style='color:#888; white-space:nowrap;'> - </span>";
    }

    return html;
}

/**
 * Форматирование статуса доступности позиции
 */
function statusFormat(cellvalue, options, rowObject)
{
    var data = cellvalue ? objectFromXml(cellvalue) : objectFromXml(rowObject);
    var tooltip = '';
    tooltip = ( data.referenceId ? "<b>" + data.referenceCategory + "</b><br/>"
        + data.referenceName + "<br/>"
        + "Соответствие поставил: <b>" + data.referenceUser + "</b> " + data.referenceDate + "<br/>" : "" )
        + "<b>" + (data.priceStatus == "BLOCKED" ? 'Цена заблокирована!' : data.available ? availDescriptions[data.available] : availDescriptions[0]) + "</b>";
    var icon = (data.priceStatus == "BLOCKED" ? '<img src="images/warning.gif"/>' : (data.available ? availIcons[data.available] : availIcons[0] ) );
    return "<span style='display:block; float:right' onmouseover='Tip(\"" + tooltip.escapeQuotes() + "\")' onmouseout='UnTip()'>" + icon + "&nbsp;</span> ";

}

/**
 * При редактировании строки подставляет в колонку гарантии элемент INPUT
 */
function warrantyElem(value, options)
{
    var el = document.createElement("input");
    el.type="text";
    el.size = '4';
    var values = value.split("&nbsp;/&nbsp;");
    el.value = values[1].replace(/<[^>]+>/g, '');
    return el;
}

/**
 * При редактировании строки получает и устанавливает значение из элемента формы
 * для передачи на сервер
 */
function warrantyValue(elem, operation, value) {
    if(operation === 'get')
        return jQ(elem).val();
    else if(operation === 'set')
        jQ('input',elem).val(value);
}

/**
 * Обновление строки после редактирования
 */
function refreshRow(rowId, response)
{
    var xml = stringToXml(response.responseText);

    var row = xml.getElementsByTagName('WidePriceItem')[0];
    var item = objectFromXml(row);
    jQ('#itemsTable').setRowData(rowId,
    {
        status: row,
        modifiers: row,
        displayWarranty: row,
        dateUpdate: item.dateUpdate,
        priceRur: row,
        priceUsd: item.priceUsd,
        priceValue: row,
        cost: row
    });
}

/**
 * Показывает/скрывает блоки установок фильтрации и поиска
 */
function toggleSettingsBlock(divId)
{
    var div = $(divId);
    if(div.visible())
        div.hide();
    else
    {
        $$("[name='settingsBlock']").each(function(el){el.hide()});
        div.show();
    }
}

/**
 * Установка параметров фильтрации позиций, определение параметров Grid и перезагрузка Grid
 */
function filterSettings()
{
    var storeSet = $('setStore').checked ?  1 : 0;
    $('filterParamsString').update(storeSet ? "Показывать позиции в наличии" : "Показывать все позиции" );

    var queryParams = jQ('#itemsTable').getGridParam('url').toQueryParams();
    queryParams['store'] = storeSet;
    var params =
    {
        url: "service/price/WideSupplierItemManager.php?" + Object.toQueryString(queryParams)
    };
    jQ('#itemsTable').clearGridData().setGridParam(params).trigger("reloadGrid");
}

/**
 * Установка параметров (полей) поиска
 */
function searchSettings()
{
    var html = $$("[name='searchParams']").select(function(box){return box.checked}).pluck('value').join(' + ');
    $('searchParamsString').update(html);
}

/**
 * Поиск и вывод в Grid найденных позиций
 */
function searchItems()
{
    var queryParams = window.location.href.toQueryParams();
    queryParams['f'] = "searchItems";
    queryParams['searchFields'] = $$("[name='searchParams']").select(function(box){return box.checked}).pluck('id').join(',');
    queryParams['searchString'] = $('search').value;
    var storeSet = $('setStore').checked ?  1 : 0;
    queryParams['store'] = storeSet;

    var params =
    {
        datatype: 'xml',
        url: "service/price/WideSupplierItemManager.php?" + Object.toQueryString(queryParams)
    };
    jQ('#itemsTable').clearGridData().setGridParam(params).trigger("reloadGrid");
    jQ('#tree').find('span.ui-state-highlight').each(function()
    {
        jQ(this).removeClass('ui-state-highlight');
    });
}

/**
 * Форматирование цены
 */
function priceFormat(cellvalue, options, rowObject)
{

    var data = cellvalue ? objectFromXml(cellvalue) : objectFromXml(rowObject);
    var html = (data.originalPrice) ? data.originalPrice : (cellvalue ? cellvalue : "0");

    return html;
}

/**
 * При редактировании строки подставляет в колонку цена элемент INPUT
 */
function priceElem(value, options)
{
    var el = document.createElement("input");
    el.type="text";
    el.size = '6';
    var values = value.split(" ");
    el.value = values[0];
    var hid = document.createElement("input");
    hid.type = 'hidden';
    hid.value = values[0];
    hid.id = "hidden";
    return [el, hid];
}

/**
 * При редактировании строки получает и устанавливает значение из элемента формы
 * для передачи на сервер
 */
function priceValue(elem, operation, value) {
    if(operation === 'get')
    {
        var oldPrice = parseFloat(jQ(elem[1]).val());
        var newPrice = parseFloat(jQ(elem[0]).val());
        var price;
        if(oldPrice && newPrice && (newPrice/oldPrice > range || oldPrice/newPrice > range))
        {
            if(confirm("Изменение цены вышло за допустимый диапазон. Всё равно принять?"))
                price = newPrice;
            else
                price = oldPrice;
        }
        else
            price = newPrice;
        return price;
    }
    else if(operation === 'set')
        jQ('input',elem).val(value);
}

/**
 * Форматирование себестоимости позиции
 */
function costFormat(cellvalue, options, rowObject, mode)
{
    var data = mode === "add" ? objectFromXml(rowObject) : objectFromXml(cellvalue);
    var html = "<span style='" + (data.priceStatus === "BLOCKED" ? 'color: red;' : '') + "'>" + data.cost + "</span>"

    return html;
}

/**
 * Форматирование себестоимости позиции
 */
function modifiersFormat(cellvalue, options, rowObject, mode)
{
    var data = mode === "add" ? objectFromXml(rowObject) : objectFromXml(cellvalue);
    var priceRur = data.priceRur ? Math.ceil(data.priceRur) : "0";
    var cost = data.cost ? data.cost : "0";
    var diff = cost - priceRur;
    var sup = '';
    var script = '';
    var style = diff > 0 ? "red" : "green";

    if(diff)
        script = "onmouseover='displayModifiers(" + data.id + ")'; onmouseout='hideModifiers(" + data.id + ")'"

    var html = "<span style='color: " + style + ";' id='diff_" + data.id + "' " + script + ">" + diff + "</span>";
	html += "<input type='hidden' id='modifiers" + data.id + "' value=''>";

    return html;
}

function displayModifiers(id)
{
    window['modifierFor'] = id;
    var tooltip = $('modifiers' + id).value;
    if(tooltip && window['modifierFor'] == id)
    {
            Tip(tooltip);
            return;
    }
    new Ajax.Request("service/price/WideSupplierItemManager.php",
    {
        method: 'get',
        parameters:
        {
            f: "getModifiers",
            itemId: id
        },
        encoding: 'windows-1251',
        asynchronous: true,
        onComplete: function (transport)
        {
            if (parseError (transport)) return;

            var modifiers = arrayFromXml(transport.responseXML.documentElement);
            if( !modifiers ) return;

            var total = 0;
            modifiers.each( function( m ) {
                m.color = m.value > 0 ? 'red' : 'green';
                m.text = (m.value > 0 ? '+' : '') + formatFloat( m.value, 2 );
                total += parseFloat( m.value );
            });

            var tooltip = Mustache.to_html(
                "<table class='fine'>\
                    {{#modifiers}}\
                    <tr>\
                        <td class='nowrap'>{{name}}</td>\
                        <td align='right' style='padding-left:1em; color:{{color}}'>{{text}}</td>\
                    </tr>\
                    {{/modifiers}}\
                    <tr style='border-top:2px solid gray'>\
                        <th align='right'>Итого</th>\
                        <th align='right' style='padding-left:1em; color:{{totalColor}}'>{{total}}</th>\
                    </tr>\
                    </table>",
                {
                    modifiers: modifiers,
                    total: formatFloat( total ),
                    totalColor: (total > 0 ? 'red' :  'green')
                }
            );
            $('modifiers' + id).value = tooltip;

            if(tooltip && window['modifierFor'] == id)
                Tip(tooltip);
        }
    });
}

function hideModifiers()
{
    window['modifierFor'] = 0;
    UnTip();
}

function renderPrices(object)
{
    var specPrice = object.specialPrice ? object.specialPrice : null;
    var button;
    var disable = ''
    if(!specPrice)
    {
        button = "<div class='addItem' onclick='addPosition(this);' id='_specialAction_" + object.id + "' title='Добавить позицию в спецЦены'>СпецЦены</div>";
        disable = "disabled";
    }
    else
        button = "<div class='deleteItem' onclick='deletePosition(this);' id='_specialAction_" + object.id + "' title='Удалить позицию из спецЦен'>СпецЦены</div>";

    var html = '';
    html += "<table class='wide grid'>";
    html += "<tr id='item_" + object.id + "'>";
    html += "<td width='18px'>" + button + "</td><td>Цена по спецпрайсу: "
        + "<input name='id' type='hidden' value='" + (specPrice ? specPrice.id : 0) + "'></input>"
        + "<input name='supplierId' type='hidden' value='" + object.supplierId + "'></input>"
        + "<input name='price' " + disable + " onchange='saveSpecial(this)' type='text' value='" + (specPrice ? specPrice.price : 0) + "' size='6'></input>"
        + "<select " + disable + " name=valuta>"
        + "<option " + (!specPrice || specPrice.valuta == 'USD' ? "selected" : "") + " value='USD'>USD</option>"
        + "<option " + (specPrice && specPrice.valuta == 'RUR' ? "selected" : "") + "value='RUR'>RUR</option>"
        + "</select></td>"
        + "<td>Наличие: "
        + "<input name='store' " + disable + " onchange='saveSpecial(this)' type='text' value='" + (specPrice ? (specPrice.store ? specPrice.store : 0) : 0) + "' size='6'></input></td>";
    var expDate;
    if(specPrice)
    {
        expDate = specPrice.expirationDate;
    }
    else
    {
        expDate = new Date();
        expDate.setDate(expDate.getDate() + 2);
        expDate = expDate.toDateString();
    }
    html += "<td>Действительна до: <input " + disable + " name='expirationDate' onchange='saveSpecial(this)' type='text' id='expDate" + object.id + "' value='" + expDate + "'></input></td></tr></table>";

    var blockedPrice = object.blockedPrice ? object.blockedPrice : null;
    if(blockedPrice && blockedPrice.status != 'DELETED')
    {
        var statusTxt =
        {
            BLOCKED: "Заблокирована",
            ACCEPTED: "Принята",
            REJECTED: "Отклонена",
            DELETED: "Отсутствует",
            ARCHIVED: "Архивная"
        };
        var acceptButton = '';
        var rejectButton = '';
        if(blockedPrice.status != 'ARCHIVED' && blockedPrice.status != 'ACCEPTED')
            acceptButton = "<input title='Принять цену' type='button' id='ACCEPT_" + blockedPrice.itemId + "' value='Принять' onclick=\"changeStatus(this, 'ACCEPTED');\">";

        if(blockedPrice.status != 'ARCHIVED' && blockedPrice.status != 'REJECTED')
            rejectButton = "<input title='Отклонить цену' type='button' id='REJECT_" + blockedPrice.itemId + "' value='Отклонить' onclick=\"changeStatus(this, 'REJECTED');\">";

        html += "<table class='wide grid'>";
        html += "<tr>"
        html += "<td>Статус</td><td>Текущая цена</td><td>Прошлая цена</td><td>Дата</td><td></td><td></td>";
        html += "</tr><tr>";
        html += "<td>" + statusTxt[blockedPrice.status] + "</td><td>" + blockedPrice.newOriginalPrice + " " + blockedPrice.newValuta + "</td>"
            + "<td>" + blockedPrice.currentOriginalPrice + " " + blockedPrice.currentValuta + "</td>"
            + "<td>" + blockedPrice.date + "</td><td>" + /*acceptButton + */"</td><td>" + /*rejectButton + */"</td>";
        html += "</tr></table>"
    }

    return html;
}

function saveSpecial(element)
{
    var tr = $(element).up('tr');
    var specItem = {itemId: tr.id.split('_').pop()};
    tr.select("[name]").each(function(input){specItem[input.readAttribute('name')] = input.value});
    specItem.f = 'saveSpecialPriceItem';
    new Ajax.Request("service/price/specprice/SpecialPriceManager.php",
    {
        method: 'get',
        parameters: specItem,
        onComplete: function(transport)
        {
            if (parseError (transport))
                return;

            var specPrice = objectFromXml(transport.responseXML.documentElement);
            tr.select("[name=id]").first().value = specPrice.id;
        }
    });
}

function addPosition(element)
{
    element = $(element);
    var tr = element.up('tr');
    tr.select("[name]").each(function(input){input.writeAttribute('disabled', null);});
    element.removeClassName('addItem').addClassName("deleteItem");
    element.writeAttribute('onclick', 'deletePosition(this);');
    element.writeAttribute('title', 'Удалить позицию из спецЦен');
}

function deletePosition(element)
{
    element = $(element);
    var tr = element.up('tr');
    var specialId = tr.select("[name='id']").first().value;
    new Ajax.Request("service/price/specprice/SpecialPriceManager.php?",
    {
        method: 'get',
        parameters:
        {
            f: 'deleteItem',
            id: specialId
        },
        onComplete: function()
        {
            tr.select("[name]").each(function(input)
            {
                if(input.readAttribute('name') != 'supplierId' || input.readAttribute('name') != 'expirationDate')
                    input.value = 0;

                input.writeAttribute('disabled', '');
            });
            element.removeClassName('deleteItem').addClassName("addItem");
            element.writeAttribute('onclick', 'addPosition(this);');
            element.writeAttribute('title', 'Добавить позицию в спецЦены');
        }
    });


}

function actAndInform( operation )
{
    var alert = "<span class='ui-icon ui-icon-alert' style='float: left; margin: 0 7px 20px 0;'></span>";
    var check = "<span class='ui-icon ui-icon-circle-check' style='float: left; margin: 0 7px 50px 0;'></span>";
    var msg = alert + operation.text;
    jQ( "#message" ).html(msg);
    jQ( "#priceDialog" ).dialog( { title: operation.header } );
    jQ( "#priceDialog" ).dialog( {
        buttons:
        {
            "Ok": function()
            {
                jQ( "#message" ).html("<center>Выполнение<div><img src='images/large_progress.gif' width='150'></div><center>");
                jQ(this).dialog( {buttons:{}} );
                jQ.get('service/price/WideSupplierItemManager.php', {f: operation.callback, supplierId: operation.supplierId}, "xml")
                    .success(function(data)
                    {
                        var reply = objectFromXml(data);
                        if(reply.warning)
                        {
                            var txt = data.getElementsByTagName ('warning')[0].firstChild.nodeValue;
                            msg = alert + "Ошибка: " + txt + "<br>Файл: " + reply.warning.file + "<br>строка: " + reply.warning.line;
                        }
                        else if(reply.error)
                        {
                            var txt = data.getElementsByTagName ('error')[0].firstChild.nodeValue;
                            msg = alert + "Ошибка: " + txt;
                        }
                        else
                        {
                            var reply = reply.root;
                            msg = check + reply.text;
                        }
                    operation.message = msg
                    closeDialog(operation);

                    })
                    .error(function(data)
                    {
                        operation.message = data.status ? (data.status + " " + data.statusText) : 'Ошибка выполнения запроса';
                        closeDialog(operation);
                    });
            },
            "Отмена": function() {jQ( this ).dialog( "close" );}
        }
    });
    jQ( "#priceDialog" ).dialog( "open" );
}

function closeDialog(operation)
{
    //var reload = !(/\s0\s/.test(operation.message));
    jQ( "#message" ).html(operation.message);
    jQ( "#priceDialog" ).dialog(
    {
        buttons:
        {
            "Закрыть": function()
            {
                jQ('#itemsTable').trigger("reloadGrid");
                jQ.get('service/price/PriceCategoriesService.php', {f: 'getCategoriesTree', supplierId: operation.supplierId}, 'html').
                    success(function(html)
                    {
                        jQ('#categoryContainer').html(html);
                        jQ("#tree").showTree(
                        {
                            closeFolders: true
                        });

                    });
                jQ( this ).dialog( "close" );
            }
        }
    });
}