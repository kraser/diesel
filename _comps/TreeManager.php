<?php
/**
 * Description of TreeManager
 *
 * @author kraser
 */
class TreeManager extends CmsComponent
{
    private $tree;
    private $list;
    private $currentList;
    public function __construct ( $alias, $parent )
    {
        parent::__construct ( $alias, $parent );
        $this->roots = [];
    }

    public function init()
    {
        parent::init();
    }

    public function createTree ( $nodes, $rootName )
    {
        $list = [];
        $tree = new Tree ();
        $tree->id = 0;
        $tree->root = $rootName;
        $keys = ArrayTools::pluck ( $nodes, "id" );
        $this->currentList = array_combine ($keys, $nodes );
        $this->currentList[0] = $tree;
        foreach ( $nodes as $node )
        {
            $this->addNode ( $node );
        }
        $this->list[$rootName] = $this->currentList;
        $this->tree[$rootName] = $tree;
    }

    public function addNode ( $node, $root = null )
    {
        $list = $root ? $this->list[$root] : $this->currentList;
        $parent = $list[$node->parentId];
        $parent->children[] = $node;
    }

    public function addLeaves ( $node, $leaves )
    {
        $node->leaves = $leaves;
    }
}

class Tree extends Model
{
    public $root;
    public $parentId;
    public $children;
    public $leaves;
}