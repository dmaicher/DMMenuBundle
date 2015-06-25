<?php

namespace DM\MenuBundle\MenuTree;

use DM\MenuBundle\Node\Node;

interface MenuTreeTraverserInterface
{
    /**
     * Indicates that traversal on current path should be stopped. So no further traversal below current node.
     */
    const STOP_TRAVERSAL = 0;

    /**
     * @param Node $root
     *
     * @return mixed
     */
    public function traverse(Node $node);
}
