<?php
namespace DM\MenuBundle\NodeVisitor;


use DM\MenuBundle\Node\Node;

/**
 * This visitor will propagate the route of the current node to the direct parent if it does not have a route yet.
 *
 * Class NodeRoutePropagator
 * @package DM\MenuBundle\NodeVisitor
 */
class NodeRoutePropagator implements NodeVisitorInterface {
    /**
     * @param Node $node
     * @return mixed|void
     */
    public function visit(Node $node)
    {
        if($node->getParent() && $node->get('route') !== null && $node->getParent()->get('route') === null) {
            $node->getParent()->set('route', $node->get('route'));
            $node->getParent()->set('route_params', $node->get('route_params'));
        }
    }
} 