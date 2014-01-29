<?php
namespace DM\MenuBundle\Menu;

class RootNodeFactory implements NodeFactory {
    /**
     * @return Node
     */
    public function create()
    {
        return new Node();
    }
} 