<?php
namespace DM\MenuBundle\Menu;

use DM\MenuBundle\MenuTree\MenuTreeBuilderInterface;
use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;
use DM\MenuBundle\Node\NodeFactoryInterface;

class MenuFactory implements MenuFactoryInterface {

    /**
     * @var MenuDefinitionHolder
     */
    protected $menuDefinitionHolder;

    /**
     * @var MenuTreeTraverserInterface
     */
    protected $menuTreeTraverser;

    /**
     * @var array
     */
    protected $cache = array();

    /**
     * @param MenuDefinitionHolder $menuDefinitionHolder
     * @param MenuTreeTraverserInterface $menuTreeTraverser
     */
    public function __construct(MenuDefinitionHolder $menuDefinitionHolder, MenuTreeTraverserInterface $menuTreeTraverser)
    {
        $this->menuDefinitionHolder = $menuDefinitionHolder;
        $this->menuTreeTraverser = $menuTreeTraverser;
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

        $menuDefinition = $this->menuDefinitionHolder->getMenuDefinition($name);

        $root = $this->getRootNode($menuDefinition['node_factory'], $menuDefinition['tree_builder']);

        $this->menuTreeTraverser->traverse($root);

        //store in "cache"
        $this->cache[$name] = $root;

        return $root;
    }

    /**
     * @param NodeFactoryInterface $nodeFactory
     * @param MenuTreeBuilderInterface $menuTreeBuilder
     * @return Node
     */
    protected function getRootNode(NodeFactoryInterface $nodeFactory, MenuTreeBuilderInterface $menuTreeBuilder)
    {
        $root = $nodeFactory->create(null);
        $menuTreeBuilder->buildTree($root, $nodeFactory);

        return $root;
    }
}
