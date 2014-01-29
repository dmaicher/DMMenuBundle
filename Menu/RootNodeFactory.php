<?php
namespace DM\MenuBundle\Menu;

class RootNodeFactory implements NodeFactory {
    /**
     * @return Node
     */
    public function create()
    {
        return Node::create(null, null);
    }
} 