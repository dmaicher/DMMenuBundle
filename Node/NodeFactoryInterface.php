<?php
namespace DM\MenuBundle\Node;

interface NodeFactoryInterface {
    /**
     * @return Node
     */
    public function create($label, array $options = array());
} 