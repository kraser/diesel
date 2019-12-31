{function renderPayments}
    {if !isset($paymentId)}{$paymentId=1}{/if}
    <tr>
        <td valign='top'>Метод оплаты:</td>
        <td colspan='4'>
            <section class="ac-container">
                {foreach from=$model['paymethods'] key=num item=pm}
                    {if $pm->id==$paymentId}
                        {$showFirst='display:block;'}
                        {$checked="checked"}
                    {else}
                        {$showFirst=''}
                        {$checked=''}
                    {/if}
                <div>
                    <input type="radio" name="payments{$prefix}" id="{$prefix}_{$pm->id}" value="{$pm->id}" {$checked}/>
                    <label for="{$prefix}_{$pm->id}">{$pm->name}</label>
                    <article class="ac-small">
                       {$pm->text}
                    </article>
                </div>
                {/foreach}
            </section>
        </td>
    </tr>
{/function}
{$isBasket=0}
{$orderTitleAdd=""}
{if !is_null($model['basket'])}
    {$products=$model['basket']->products}
    {$total=$model['basket']->total}
    {$isBasket=1}
    {$orderTitleAdd=" Можно добавить корзину к заказу, нажав <b>+</b> "}
{/if}
{$userInfo=$model['userInfo']}

{if $model['orders']}
    {$basketBtnTxt="Оформить новый заказ"}
{else}
    {$basketBtnTxt="Оформить заказ"}
{/if}
{if !is_null($model['basket']) && count($products)>0}
<table id="basketOrder">
    <caption>Товары в корзине на оформление заказа
    <tr>
        <th>Код</th>
        <th>Наименование</th>
        <th>Цена за 1</th>
        <th>Кол-во</th>
        <th>Стоимость</th>
    </tr>
    <tbody id="inBasketList">
{foreach from=$products item=product}
    <tr>
        <td>{$product->id}</td>
        <td>{$product->name}</td>
        <td>{$product->price}</td>
        <td>{$product->quantity}</td>
        <td>{$product->total}</td>
   </tr>
{/foreach}
    <tr>
        <td colspan='4'>Итого:</td>
        <td>{$total}</td>
    </tr>



    </tbody>
    <tbody id="basketOrderInfo">
    <tr>
        <td>Адрес доставки:</td>
        <td colspan='4' id="basketAddress" valign="top" name="address" title="Клик для изменения реквизитов">
            <input type="text" name="address" value="{$userInfo->address}" style="display:none;">
            <span style="display:inline;">{$userInfo->address}</span>
        </td>
    </tr>
    <tr>
        <td>Конт. телефон:</td>
        <td colspan='4' name="phone" title="Клик для изменения реквизитов">
            <input type="text" name="phone" value="{$userInfo->phone}" style="display:none;">
            <span style="display:inline;">{$userInfo->phone}</span>
        </td>
    </tr>
    {if !empty($model['paymethods'])}
        {call renderPayments prefix="basket"}
    {/if}
    <tr>
        <td colspan='5'><input type='button' value='{$basketBtnTxt}' id="addNew"></td>
    </tr>
    </tbody>
</table>
{/if}
<hr>
{if $model['orders']}
    {$orders=$model['orders']}
    <table width='100%' id="orderList">
        <caption>Заказы оформленные, но еще не принятые к исполнению{$orderTitleAdd}</caption>
    {foreach from=$orders item=order}
        <tr id="{$order->id}">
            <th colspan='4'>Заказ {$order->id} от {$order->date}</th>
            <th>
                <img title="Развернуть заказ" name="minifier" class="switchDown" src="/images/switch.png" />&nbsp;
                {if $isBasket==1}<input type='button' title='Добавить корзину к этому заказу' value="+">{/if}
            </th>
        </tr>
        <tbody id="order_{$order->id}" style="display:none;">
        {foreach from=$order->products item=product}
        <tr>
            <td>{$product->product}</td>
            <td>{$product->name}</td>
            <td>{$product->price}</td>
            <td>{$product->count}</td>
            <td>{$product->price*$product->count}</td>
        </tr>
        {/foreach}
        <tr>
            <td>
                Адрес доставки:
                <input type="hidden" value="{$order->id}" name="orderId">
            </td>
            <td colspan='4' id="order_{$order->id}_Address" name="address" valign="top" title="Клик для изменения реквизитов">
                <input name="address"  type="text" value="{$order->address}" style="display:none;">
                <span style="display:inline;">{$order->address}</span>
            </td>
        </tr>
        <tr>
            <td>
                Конт. телефон:
            </td>
            <td colspan='4' name="phone" title="Клик для изменения реквизитов">
                <input name="phone" type="text" value="{$order->phone}" style="display:none;">
                <span style="display:inline;">{$order->phone}</span>
            </td>
        </tr>
        <tr>
            <td valign='top'>Метод оплаты:</td>
            <td colspan='4'>
            {if !empty($model['paymethods'])}
                {$prefix="order_`$order->id`"}
                {$model['paymethods'][$order->paymethod]->name}
            {/if}
            </td>
        </tr>
        </tbody>
    {/foreach}
    </table>
{/if}

