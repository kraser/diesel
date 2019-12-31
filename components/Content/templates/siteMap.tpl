{$map=$model.sitemap}
{*{serialize($map)}*}
{function renderMapBranch}
<ul>
    {foreach from=$branch item=doc}
    <li>
        <a href="{$doc->link}">{$doc->title}</a>
        {if count(count($doc->docs))}
            {call renderMapBranch branch=$doc->docs}
        {/if}
        {if count(count($doc->children))}
            {call renderMapBranch branch=$doc->children}
        {/if}
    </li>
    {/foreach}
</ul>
{/function}

<div id="siteMap">
{call renderMapBranch branch=$map}
</div>