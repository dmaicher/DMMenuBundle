<?php
namespace DM\MenuBundle\Tests\NodeVisitor;

use DM\MenuBundle\Node\Node;
use DM\MenuBundle\NodeVisitor\NodeActivator;
use Symfony\Component\HttpFoundation\Request;

class NodeActivatorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var NodeActivator
     */
    protected $activator;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Node
     */
    protected $node;

    public function setUp()
    {
        $this->request = $this
            ->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock()
        ;

        $this->activator = new NodeActivator($this->request);
        $this->node = new Node();
    }

    /**
     * @dataProvider getTestData
     *
     * @param array $routes
     * @param $requestRoute
     * @param $expectedIsActive
     */
    public function testVisit(array $routes, $requestRoute, $expectedIsActive)
    {
        $this->node->set('additional_active_routes', $routes);

        $this->request
            ->expects($this->any())
            ->method('get')
            ->with('_route')
            ->will($this->returnValue($requestRoute))
        ;

        $this->activator->visit($this->node);

        $this->assertEquals($expectedIsActive, $this->node->isActive());
    }

    public function getTestData()
    {
        return array(
            array(array(), 'some_route', false),
            array(array('some_route', 'some_other_route'), 'some_different_route', false),
            array(array('some_route'), 'some_route', true),
            array(array('some_route', 'some_other_route'), 'some_other_route', true)
        );
    }
} 