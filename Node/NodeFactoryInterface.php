<?php

namespace DM\MenuBundle\Node;

interface NodeFactoryInterface
{
    /**
     * @param $label
     *
     * @return Node
     */
    public function create($label);
}
