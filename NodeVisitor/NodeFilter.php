<?php
namespace DM\MenuBundle\NodeVisitor;

use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * This visitor will remove the node from the tree if not all necessary permissions are granted by securityContext.
 *
 * Class NodeFilter
 * @package DM\MenuBundle\NodeVisitor
 */
class NodeFilter implements NodeVisitorInterface {

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function __construct(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param Node $node
     * @return mixed|void
     */
    public function visit(Node $node)
    {
        foreach($node->getRequiredRoles() as $role) {
            if(!$this->securityContext->getToken() || !$this->securityContext->isGranted($role)) {
                $node->getParent()->removeChild($node);
                return MenuTreeTraverserInterface::STOP_TRAVERSAL;
            }
        }
    }
} 