<?php

namespace DM\MenuBundle\NodeVisitor;

use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * This visitor will remove the node from the tree if not all necessary permissions are granted by securityContext.
 *
 * Class NodeFilter
 */
class NodeFilter implements NodeVisitorInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authChecker)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authChecker = $authChecker;
    }

    /**
     * @param Node $node
     *
     * @return mixed|void
     */
    public function visit(Node $node)
    {
        foreach ($node->getRequiredRoles() as $role) {
            if (!$this->tokenStorage->getToken() || !$this->authChecker->isGranted($role)) {
                $node->getParent()->removeChild($node);

                return MenuTreeTraverserInterface::STOP_TRAVERSAL;
            }
        }
    }
}
