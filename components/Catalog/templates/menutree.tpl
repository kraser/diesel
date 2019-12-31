{$menu=$model.menu}
{function renderBranch level=0}
    <ul class="treeLevel_{$level}">
    {foreach item=branch from=$branches}
        <li id='category_{$branch->id}'>
            <div data-link="{$branch->link}" class="item" name='inFolder' style='font-weight: normal;' title='{$branch->name}'>{$branch->name}</div>
            {if count($branch->subCategories)}{call renderBranch branches=$branch->subCategories level=$level+1}{/if}
        </li>
    {/foreach}
    </ul>
{/function}

<ul id="tree">
    <li id='category_0'>
        <div data-link="none" class="item" name='inFolder' style='font-weight: normal;' title='Каталог'>Каталог</div>
        {call renderBranch branches=$menu->subCategories}
    </li>
</ul>
