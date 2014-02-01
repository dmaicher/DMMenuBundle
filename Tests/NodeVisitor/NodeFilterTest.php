<?php
namespace DM\MenuBundle\Tests\NodeVisitor;

use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;
use DM\MenuBundle\NodeVisitor\NodeFilter;
use Symfony\Component\Security\Core\SecurityContextInterface;

class NodeFilterTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var NodeFilter
     */
    protected $filter;

    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

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
        $this->securityContext = $this
            ->getMockBuilder('Symfony\Component\Security\Core\SecurityContextInterface')
            ->getMock()
        ;

        $this->filter = new NodeFilter($this->securityContext);
        $this->node = new Node();
        $this->parent = $this
            ->getMockBuilder('DM\MenuBundle\Node\Node')
            ->getMock()
        ;

        $this->node->setParent($this->parent);
    }

    /**
     * @dataProvider getTestData
     *
     * @param array $roles
     * @param $getTokenReturn
     * @param $isGrantedReturn
     * @param $expectsFiltered
     */
    public function testVisit(array $roles, $getTokenReturn, $isGrantedReturn, $expectsFiltered)
    {
        $this->node->setRequiredRoles($roles);

        $this->securityContext
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($getTokenReturn))
        ;

        $this->securityContext
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue($isGrantedReturn))
        ;

        if($expectsFiltered) {
            $this->parent->expects($this->once())->method('removeChild');
        }
        else{
            $this->parent->expects($this->never())->method('removeChild');
        }

        $return = $this->filter->visit($this->node);

        if($expectsFiltered) {
            $this->assertSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        }
        else{
            $this->assertNotSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        }
    }

    public function getTestData()
    {
        return array(
            array(array(), true, true, false),
            array(array('FOO'), true, true, false),
            array(array('FOO'), true, false, true),
            array(array('FOO'), false, true, true)
        );
    }
} 