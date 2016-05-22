{$slideList=$model.slideList}
<div class="slider">
    <ol>
    {foreach from=$slideList item=slide}
        {$image=image($slide.image, 255, 255, 'png', "#FFFFFF" )}
        <li>
            <h2><span>{$slide.name}</span></h2>
            <div>
                <a href="{$slide.link}">
                <img style="float: right;" src="{$slide.image}" title="{$slide.name}" alt="{$slide.name}"/>
                <ul>
                {$slide.text}
                </ul>
                </a>
            </div>
        </li>
    {/foreach}
    </ol>
</div>
