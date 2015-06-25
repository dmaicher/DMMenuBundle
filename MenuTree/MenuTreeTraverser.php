<?php

namespace DM\MenuBundle\MenuTree;

use DM\MenuBundle\Node\Node;
use DM\MenuBundle\NodeVisitor\NodeVisitorInterface;

class MenuTreeTraverser implements MenuTreeTraverserInterface
{
    /**
     * @var array
     */
    protected $visitors = array();

    /**
     * @param Node $node
     *
     * @return mixed|void
     */
    public function traverse(Node $node)
    {
        if (!$node->isRootNode()) {
            foreach ($this->visitors as $visitor) {
                $result = $visitor->visit($node);
                if ($result === self::STOP_TRAVERSAL) {
                    return;
                }
            }
        }

        foreach ($node->getChildren() as $child) {
            $this->traverse($child);
        }
    }

    /**
     * @param NodeVisitorInterface $visitor
     */
    public function addVisitor(NodeVisitorInterface $visitor)
    {
        $this->visitors[] = $visitor;
    }

    /**
     * @return array
     */
    public function getVisitors()
    {
        return $this->visitors;
    }
}
