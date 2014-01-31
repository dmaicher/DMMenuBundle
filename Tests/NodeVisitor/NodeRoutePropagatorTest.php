<?php
namespace DM\MenuBundle\Tests\NodeVisitor;

use DM\MenuBundle\Node\Node;
use DM\MenuBundle\NodeVisitor\NodeRoutePropagator;

class NodeRoutePropagatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var NodeActivator
     */
    protected $propagator;

    /**
     * @var Node
     */
    protected $node;

    /**
     * @var Node
     */
    protected $parent;

    public function setUp()
    {
        $this->propagator = new NodeRoutePropagator();
        $this->node = new Node();
        $this->parent = new Node();
        $this->parent->addChild($this->node);
    }

    /**
     * @dataProvider getTestData
     */
    public function testVisit($childRoute, $parentRoute, $expectedParentRoute)
    {
        $this->node->set('route', $childRoute);
        $this->parent->set('route', $parentRoute);

        $this->propagator->visit($this->node);

        $this->assertEquals($expectedParentRoute, $this->parent->get('route'));
    }

    public function getTestData()
    {
        return array(
            array(null, 'some_route', 'some_route'),
            array('some_route', null, 'some_route'),
            array('some_route', 'some_other_route', 'some_other_route'),
        );
    }
} 