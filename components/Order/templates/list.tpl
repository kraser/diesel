{$orders=$model.orders}
{$statuses=$model.statuses}
{$paymethods=$model.paymethods}
{$shops=$model.shops}
{$basket=$model.basket}
{$user=$model.user}
{if isset($basket)}
    <table class='bordered' width="100%">
        <tr>
            <th>Наименование</th>
            <th>Количество</th>
            <th>Цена за 1</th>
            <th>Всего</th>
            <th>Комментарий</th>
        </tr>
        {foreach from=$basket->products item=basketItem}
        <tr>
            <td><a href='{$basketItem->link}'>{$basketItem->name} {$model.features[$basketItem->featureId]->name}: {$basketItem->featureValue}</a></td>
            <td>{$basketItem->quantity}</td>
            <td>{$basketItem->price}</td>
            <td>{$basketItem->total}</td>
            <td>{$basketItem->info}</td>
        </tr>
        {/foreach}
        <tr>
            <td colspan='5' style='padding-left: 10px;text-align: left;'><b>Итого товаров: {$basket->count} на сумму: {$basket->total} руб.</b></td>
        </tr>
    </table>
    <form action="/privateOffice/createOrder" id="orderForm" class="mainform" method="post">
        <h2>Контактные данные</h2>
        <label for='Oname'>Имя, фамилия
            <input type="text" name="name" id="Oname" placeholder="Имя, фамилия" value="{if $user}{$user->name}{/if}" />
        </label>
        <label for='Omail'>E-mail
            <input type="text" name="mail" id="Omail" placeholder="E-mail" value="{if $user}{$user->email}{/if}" />
        </label>
        <label for='Omail'>Телефон
            <input type="text" name="phone" id="Ophone" placeholder="Телефон" value="{if $user}{$user->phone}{/if}" />
        </label>
        <h2>Доставка</h2>
        <textarea name="address" id="Oaddress" placeholder="Адрес доставки">{if $user}{$user->address}{/if}</textarea>
        {if count($paymethods)}
        <h2 class="block-title disabled">Оплата</h2>
        <section class="ac-container">
            {foreach from=$paymethods item=pm}
            {if !isset($showFirst)}
                {$showFirst='checked'}
            {else}
                {$showFirst=''}
            {/if}
            <div>
                <input type="radio" name="payment" id="PaymentMethod{$pm->id}" value="{$pm->id}" {$showFirst}/>
                <label for="PaymentMethod{$pm->id}">{$pm->name}</label>
                <article class="ac-small">{$pm->text}</article>
            </div>
            {/foreach}
        </section>
        {/if}
        <input type="button" class="button" id="button-order" value="Оформить заказ"/>
    </form>
{/if}
Заказов: {count($orders)}
<table class='bordered' width="100%">
    <tr>
        <th style="width:30px;">№</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Телефон</th>
        <th>Адрес</th>
        <th>Комментарий</th>
        <th>Оплата</th>
        <th>Сумма заказа</th>
        {if count($shops)}
        <th>Точка выдачи</th>
        {/if}
        <th>Статус</th>
        <th>Дата</th>
    </tr>
{foreach from=$orders item=order}
    <tr>
        <td>{$order->id}</td>
        <td>{$order->name}</td>
        <td>{$order->mail}</td>
        <td>{$order->phone}</td>
        <td>{$order->address}</td>
        <td>{$order->comment}</td>
        <td>{$paymethods[$order->paymethod]->name}</td>
        <td>{$order->orderSum}</td>
        {if count($shops)}
        <td>{$shops[$order->shopId]->name}</td>
        {/if}
        <td>{$statuses[$order->status]->name}</td>
        <td>{$order->date}</td>
    </tr>
{/foreach}
</table>