<?php
/**
 * Description of PagerWidget
 *
 * @author kraser
 */
class PaginationWidget extends CmsWidget
{
    private $buttons;
    public function __construct ( $parent )
    {
        parent::__construct ( "Pagination", $parent );
        $this->buttons = 5;
    }

    public function setButtons ( $quantity )
    {
        $this->buttons = $quantity;
    }

    private $paginator;
    public function setPaginator ( $paginator )
    {
        $this->paginator = $paginator;
    }

    public function render ()
    {
        $parent = $this->paginator->parent;
        $pageSize = $parent->pageSize;
        $itemsCount = $this->paginator->itemsCount;
        $currentPage = $this->paginator->offset;
        $pagesCount = ceil ( $itemsCount / $pageSize );

        $left = $currentPage - 1;
        if ( $left < floor ( $this->buttons / 2 ) )
            $start = 1;
        else
            $start = $currentPage - floor ( $this->buttons / 2 );

        $end = $start + $this->buttons - 1;
        if ( $end > $pagesCount )
        {
            $start -= ($end - $pagesCount);
            $end = $pagesCount;
            if ( $start < 1 )
                $start = 1;
        }

        $model =
        [
            'pagesCount' => $pagesCount,
            'currentPage' => $currentPage,
            'url' => Starter::app ()->urlManager->getUrlPart ( 'path' ),
            'query' => Starter::app ()->urlManager->createRequestParameters (),
            'pageUrlAlias' => 'page',
            'start' => $start,
            'end' => $end,
        ];
        return TemplateEngine::view ( "widgets/paging", $model );
    }
}