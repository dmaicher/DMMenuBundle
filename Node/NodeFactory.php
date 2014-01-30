<?php
namespace DM\MenuBundle\Node;

class NodeFactory implements NodeFactoryInterface {
    /**
     * @return Node
     */
    public function create($label, array $options = array())
    {
        $options['label'] = $label;
        return new Node($options);
    }
} 