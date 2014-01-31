<?php
namespace DM\MenuBundle\NodeVisitor;

use DM\MenuBundle\Node\Node;

interface NodeVisitorInterface {
    /**
     * @param Node $node
     * @return mixed
     */
    public function visit(Node $node);
} 