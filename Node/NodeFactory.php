<?php
namespace DM\MenuBundle\Node;

class NodeFactory implements NodeFactoryInterface {
    /**
     * @param $label
     * @return Node
     */
    public function create($label = null)
    {
        return new Node($label);
    }
} 