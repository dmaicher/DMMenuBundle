<?php
namespace DM\MenuBundle\Menu;

use DM\MenuBundle\Node\Node;
use DM\MenuBundle\Node\NodeFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\SecurityContext;

class MenuFactory implements MenuFactoryInterface {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * @var string
     */
    protected $currentRoute;

    /**
     * @var array
     */
    protected $menuDefinitions;

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->securityContext = $container->get('security.context');
        $this->currentRoute = $container->get('request')->get('_route');
        $this->menuDefinitions = $container->getParameter('dm_menu.menu_definitions');
        $this->container = $container;
    }

    /**
     * @param $name
     * @return Node
     * @throws \InvalidArgumentException
     */
    public function create($name)
    {
        //already created for this request?
        if(isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        if(!isset($this->menuDefinitions[$name])) {
            throw new \InvalidArgumentException("menu with name '{$name}' not found!");
        }

        $menuDefinition = $this->menuDefinitions[$name];

        $nodeTreeBuilder = $this->getObjectFromConfigValue($menuDefinition['tree_builder']);

        if(!$nodeTreeBuilder instanceof MenuTreeBuilderInterface) {
            throw new \InvalidArgumentException("configured tree_builder has to be of type MenuTreeBuilderInterface");
        }

        if(isset($menuDefinition['node_factory'])) {
            $nodeFactory = $this->getObjectFromConfigValue($menuDefinition['node_factory']);

            if(!$nodeTreeBuilder instanceof NodeFactoryInterface) {
                throw new \InvalidArgumentException("configured node_factory has to be of type NodeFactoryInterface");
            }
        }
        else {
            $nodeFactory = $this->container->get('dm_menu.node_factory');
        }

        $root = $nodeFactory->create(null);
        $nodeTreeBuilder->buildTree($root, $nodeFactory);

        $this->traverseTree($root);

        //store in "cache"
        $this->cache[$name] = $root;

        return $root;
    }

    /**
     * @param Node $node
     */
    protected function traverseTree(Node $node)
    {
        if(!$this->isNodeVisible($node)) {
            $node->getParent()->removeChild($node);
            return;
        }

        if($this->isNodeActive($node)) {
            $node->setActive(true);
        }

        $firstChildWithRoute = null;
        foreach($node->getChildren() as $child) {
            $this->traverseTree($child);
            if($firstChildWithRoute === null && $child->getParent() !== null && $child->hasRoute()) {
                $firstChildWithRoute = $child;
            }
        }

        if(!$node->isRootNode() && !$node->hasRoute() && $firstChildWithRoute !== null) {
            $node->set('route', $firstChildWithRoute->get('route'));
            $node->set('route_params', $firstChildWithRoute->get('route_params'));
        }
    }

    /**
     * @param Node $node
     * @return bool
     */
    protected function isNodeVisible(Node $node)
    {
        if($node->isRootNode()) {
            return true;
        }

        foreach($node->get('required_roles') as $role) {
            if(!$this->securityContext->getToken() || !$this->securityContext->isGranted($role)) {
               return false;
            }
        }

        return true;
    }

    /**
     * @param Node $node
     * @return bool
     */
    protected function isNodeActive(Node $node)
    {
        foreach($node->get('additional_active_routes') as $route) {
            if($route == $this->currentRoute) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @return object
     * @throws \InvalidArgumentException
     */
    protected function getObjectFromConfigValue($value)
    {
        if($this->container->has($value)) {
            return $this->container->get($value);
        }

        if(class_exists($value)) {
            return new $value();
        }

        throw new \InvalidArgumentException("value '{$value}' is neither a valid class nor an existing service!");
    }
} 