<?php
namespace DM\MenuBundle\Menu;

interface NodeFactory {
    /**
     * @return Node
     */
    public function create($label, array $options = array());
} 