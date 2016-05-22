jQuery(document).ready(function ()
{
    initOrderGrid();
    initBasketGrid();
});

function initOrderGrid()
{
    var caption = "Здесь будет caption";
    renderOrderGrid(caption);
    var params =
    {
        datatype: 'xml',
        url: "/order/list"
    };
    $('#ordersList').clearGridData().setGridParam(params).trigger("reloadGrid");
    
    /*
    $.ajax("/order/list",
    {
        type: "get",
        dataType: "xml",
        success: function(xml)
        {
            
            $('#ordersList').clearGridData().setGridParam({}).trigger("reloadGrid");
        }
    });
    */
}

function renderOrderGrid(caption)
{
    $('#ordersList').jqGrid(
    {
        datatype: 'local',
        //width: 952,
        caption: caption,
        autowidth: true,
        height: '100%',
        colNames:['#', '', '', 'Кол-во', 'Статус', 'Сумма', "Дата"],
        colModel :
        [
            {
                name: 'id',
                index: 'id',
                width: 65,
                fixed: true,
                sortable: false,
                align: 'center', 
                resize: false,
                //formatter:'actions',
                /*formatoptions:
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
                    url: "basket/Edit",
                    delOptions: {},
                    editOptions : {}
                },*/
                xmlmap: '[id]'
            },
            {name:'orderId', index:'orderId', hidden: true, editable: false, xmlmap:"[orderId]"},
            {name:'name', index:'name', width: 557, formatter: orderInfoFormat/*, xmlmap:"[name]"*/},
            //{name:'price', index:'price', width: 60, align: 'center', xmlmap:"[price]"},
            {name:'count', index:'count', width: 60, align: 'center', editable:true, xmlmap:"[count]"},
            {name:'status', index:'status', width: 60, align: 'center', xmlmap:"[status]"},
            {name:'summ', index:'summ', width: 70, align: 'center', xmlmap:'[summ]', summaryType:'sum'},
            {name:'dateUpdate', index:'dateUpdate', width: 80, align: 'center', xmlmap:"[dateUpdate]"}
        ],
        onCellSelect: function(rowid, iCol, cellcontent, e)
        {
            if(iCol != 0)
                selectRow.call(this, rowid);
        },
        gridComplete : function()
        {
            $('#ordersList .jqgrow').each(function(n, row)
            {
                $(row).addClass( ( 0 == n % 2 ) ? 'even' : 'odd' );
            });
            
        },
        subGrid: true,
        subGridOptions:
        {
            plusicon : "ui-icon-triangle-1-e",
            minusicon : "ui-icon-triangle-1-s", 
            openicon : "ui-icon-arrowreturn-1-e", 
            // load the subgrid data only once 
            // and the just show/hide 
            reloadOnExpand : false, 
            // select the row when the expand column is clicked 
            selectOnExpand : true
        },
        
        subGridRowExpanded: function(subgridId, rowId) 
        { 
            var subgridTableId, pagerId; 
            subgridTableId = subgridId + "_t"; 
            pagerId = "p_" + subgridTableId; 
            $("#" + subgridId).html("<table id='" + subgridTableId + "' class='scroll'></table><div id='" + pagerId + "' class='scroll'></div>"); 
            jQuery("#" + subgridTableId).jqGrid(
            { 
                datatype: 'local',
                autowidth: true,
                colNames: ['','Наименование позиции','Кол-во','Цена','Ед. изм.', 'Сумма'], 
                colModel: 
                [ 
                    {name:"orderId", index:"id", width:20, align:"center", xmlmap:"[orderId]"}, 
                    {name:"name", index:"name", width:500, align:"center", xmlmap:"[name]"}, 
                    {name:"count", index:"count", width:65, align:"center", xmlmap:"[count]"}, 
                    {name:"price", index:"price", width:65, align:"center", xmlmap:"[price]"}, 
                    {name:"unit", index:"unit", width:65, align:"center", xmlmap:"[unit]"}, 
                    {name:"summ", index:"summ", width:65, align:"center", sortable:false, xmlmap:"[summ]"}
                ], 
                rowNum:100,
                xmlReader:
                {
                    root:'root',
                    row: "OrderItem", 
                    repeatitems: false 
                },
                //pager: pagerId, 
                sortname: 'num', 
                sortorder: "asc", 
                height: '100%' 
            });
            var data = $('#ordersList').getRowData();
            var params =
            {
                url:"/order/getOrderProducts?orderId=" + rowId, 
                datatype: "xml" 
            };
            jQuery("#" + subgridTableId).clearGridData().setGridParam(params).trigger("reloadGrid");
    
    //        jQuery("#" + subgridTableId).jqGrid('navGrid',"#" + pagerId, {edit:false,add:false,del:false}); 
        },
        loadComplete : function(data)
        {
            if(data.documentElement && data.documentElement.childNodes.length == 0 )
            {
                var target = $('#ordersList').find('tr');
                $('<tr><td colspan="9" align="center"><div class="warning" style="margin:1em">Ничего не найдено</div></td></tr>').insertAfter(target);
            }
        },
        grouping: false,
        groupingView :
        {
            groupField : ['orderId'],
            groupColumnShow : [false],
            groupText : ['<b>{0} - {1} Позиций</b>' ], 
            groupCollapse : true,
            plusicon: 'ui-icon-plus',
            minusicon: 'ui-icon-minus'
        },
        xmlReader:
        {
            root:'root',
            row:'OrderInfo',
            repeatitems: false
        },
        rowNum: 10000/*,
        pager: '#ordersPager'*/
    });
    /*
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
    */
}

function refreshRow(rowId, response)
{
    var xml = stringToXml(response.responseText);

    var row = xml.getElementsByTagName('OrderItem')[0];
    var item = objectFromXml(row);
    $('#ordersList').setRowData(rowId,
    {
        count: row
    });
}

function orderInfoFormat(cellvalue, options, rowObject)
{
    var data = objectFromXml(rowObject);
    var html = "<b>Заказ #" + data.id + " от " + data.dateUpdate + "</b>";

    return html;
}

function initBasketGrid()
{
    var caption = "Здесь будет Basket caption";
    renderBasketGrid(caption);
    var params =
    {
        datatype: 'xml',
        url: "/basket/getBasketList"
    };
    $('#basketList').clearGridData().setGridParam(params).trigger("reloadGrid");

    /*
    $.ajax("/order/list",
    {
        type: "get",
        dataType: "xml",
        success: function(xml)
        {

            $('#ordersList').clearGridData().setGridParam({}).trigger("reloadGrid");
        }
    });
    */
}

function renderBasketGrid(caption)
{
    $('#basketList').jqGrid(
    {
        datatype: 'local',
        //width: 952,
        caption: caption,
        autowidth: true,
        height: '100%',
        colNames:['', '', 'Наименование', 'Цена' , 'Кол-во', 'Ед. изм.', 'Сумма'/*, "Дата"*/],
        colModel :
        [
            {
                name: 'id',
                index: 'id',
                width: 65,
                fixed: true,
                sortable: false,
                resize: false,
                formatter:'actions',
                formatoptions:
                {
                    keys:true,
                    editbutton : true,
                    delbutton : false,
                    editformbutton: false,
                    onEdit : null,
                    onSuccess: null,
                    afterSave:refreshBasketRow,
                    onError: null,
                    afterRestore: null,
                    extraparam: {oper:'edit'},
                    url: "/basket/EditItem",
                    delOptions: {},
                    editOptions : {}
                },
                xmlmap: 'id'
            },
            {name:'categoryId', index:'categoryId', hidden: true, editable: false, xmlmap:"[categoryId]"},
            {name:'name', index:'name', width: 557, xmlmap:"[name]"},
            {name:'price', index:'price', width: 60, align: 'center', xmlmap:"[price]"},
            {name:'count', index:'count', width: 60, align: 'center', editable: true, xmlmap:"[count]"},
            {name:'unit', index:'unit', width: 60, align: 'center', xmlmap:"[unit]"},
            {name:'summ', index:'summ', width: 70, align: 'center', xmlmap:'[summ]', summaryType:'sum'}//,
            //{name:'dateUpdate', index:'dateUpdate', width: 80, align: 'center', xmlmap:"[dateUpdate]"}
        ],
        onCellSelect: function(rowid, iCol, cellcontent, e)
        {
            if(iCol != 0)
                selectRow.call(this, rowid);
        },
        gridComplete : function()
        {
            $('#basketList .jqgrow').each(function(n, row)
            {
                $(row).addClass( ( 0 == n % 2 ) ? 'even' : 'odd' );
            });
            
        },

        loadComplete : function(data)
        {
            if(data.documentElement && data.documentElement.childNodes.length == 0 )
            {
                var target = $('#basketList').find('tr');
                $('<tr><td colspan="9" align="center"><div class="warning" style="margin:1em">Ничего не найдено</div></td></tr>').insertAfter(target);
            }
        },
        grouping: false,
        groupingView :
        {
            groupField : ['orderId'],
            groupColumnShow : [false],
            groupText : ['<b>{0} - {1} Позиций</b>' ],
            groupCollapse : true,
            plusicon: 'ui-icon-plus',
            minusicon: 'ui-icon-minus'
        },
        xmlReader:
        {
            root:'root',
            row:'BasketItem',
            repeatitems: false
        },
        rowNum: 10000/*,
        pager: '#basketPager'*/
    });
    /*
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
    */
}

function refreshBasketRow(rowId, response)
{
    var xml = stringToXml(response.responseText);

    var row = xml.getElementsByTagName('BasketItem')[0];
    var item = objectFromXml(row);
    $('#basketList').setRowData(rowId,
    {
        count: row
    });
}
