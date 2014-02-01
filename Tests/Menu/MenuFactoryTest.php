<?php
namespace DM\MenuBundle\Tests\Menu;

use DM\MenuBundle\Menu\MenuFactory;

class MenuFactoryTest extends \PHPUnit_Framework_TestCase {

    protected $nodeFactory;

    protected $treeBuilder;

    protected $menuDefinitionHolder;

    protected $menuTreeTraverser;

    /**
     * @var MenuFactory
     */
    protected $menuFactory;

    public function setUp()
    {
        $this->nodeFactory = $this
            ->getMock('DM\MenuBundle\Node\NodeFactoryInterface')
        ;

        $this->treeBuilder = $this
            ->getMock('DM\MenuBundle\MenuTree\MenuTreeBuilderInterface')
        ;

        $this->menuDefinitionHolder = $this
            ->getMock('DM\MenuBundle\Menu\MenuDefinitionHolder')
        ;

        $this->menuTreeTraverser = $this
            ->getMock('DM\MenuBundle\MenuTree\MenuTreeTraverserInterface')
        ;

        $this->menuFactory = new MenuFactory($this->menuDefinitionHolder, $this->menuTreeTraverser);
    }

    public function testCreate()
    {
        $node = $this->getMock('DM\MenuBundle\Node\Node');

        $this->nodeFactory
            ->expects($this->once())
            ->method('create')
            ->will($this->returnValue($node))
        ;

        $this->menuDefinitionHolder
            ->expects($this->once())
            ->method('getMenuDefinition')
            ->with('name')
            ->will($this->returnValue(array(
                'tree_builder' => $this->treeBuilder,
                'node_factory' => $this->nodeFactory
            )))
        ;

        $this->menuTreeTraverser
            ->expects($this->once())
            ->method('traverse')
            ->with($node)
        ;

        $this->treeBuilder
            ->expects($this->once())
            ->method('buildTree')
            ->with($node, $this->nodeFactory)
        ;

        $this->menuFactory->create('name');
    }
} 