<?php

namespace DM\MenuBundle\NodeVisitor;

use DM\MenuBundle\Node\Node;
use Symfony\Component\HttpFoundation\Request;

/**
 * This visitor will set a node to active if one of its routes matches the current route of the request.
 *
 * Class NodeActivator
 */
class NodeActivator implements NodeVisitorInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * @param Node $node
     *
     * @return mixed|void
     */
    public function visit(Node $node)
    {
        if (!$this->request) {
            return;
        }

        if (in_array($this->request->get('_route'), $node->getAllActiveRoutes())) {
            $node->setActive(true);
        }
    }
}
