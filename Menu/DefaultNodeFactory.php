<?php
namespace DM\MenuBundle\Menu;

class DefaultNodeFactory implements NodeFactory {
    /**
     * @return Node
     */
    public function create($label, array $options = array())
    {
        $options['label'] = $label;
        return new Node($options);
    }
} 