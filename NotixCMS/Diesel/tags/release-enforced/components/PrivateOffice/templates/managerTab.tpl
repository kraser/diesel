{$user=$model.user}
<div style="height:15px;">
    <span style="float:right;text-align:right;"><a href="/privateOffice/logout">Выйти</a></span>{$user->name}
</div>

<div style="margin-top:5px">
{if $user->status != "client"}
    Клиент: <span class="editable autocomplete" field="user">Выберите клиента</span>
{/if}
    <input type="hidden" id="clientId" value="{$model.clientId}"/>
    <input type="hidden" id="userId" value="{$user->id}"/>
    <input type="hidden" id="userStatus" value="{$user->status}"/>
</div>


<div id="tabs">
    <ul>
        <li><a href="#{$model.mainTab.alias}">{$model.mainTab.tabName}</a></li>
    {foreach from=$model.tabs item=tab key=alias}
        <li><a href="#{$alias}">{$tab.tabName}</a></li>
    {/foreach}
    </ul>
    <div id="{$model.mainTab.alias}" style="padding-left: 5px; padding-right: 5px;">{$model.mainTab.tabName}{$model.mainTab.html}</div>
{foreach from=$model.tabs item=tab key=alias}
    <div id="{$alias}" name="serviceTab" style="padding-left: 5px; padding-right: 5px;">
        {$tab.html}
    </div>
{/foreach}
</div>
