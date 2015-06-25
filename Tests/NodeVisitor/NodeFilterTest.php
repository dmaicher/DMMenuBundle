<?php

namespace DM\MenuBundle\Tests\NodeVisitor;

use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;
use DM\MenuBundle\NodeVisitor\NodeFilter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NodeFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NodeFilter
     */
    private $filter;

    /**
     * @var TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authChecker;

    /**
     * @var Node
     */
    private $node;

    /**
     * @var Node|\PHPUnit_Framework_MockObject_MockObject
     */
    private $parent;

    public function setUp()
    {
        $this->tokenStorage = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')
            ->getMock()
        ;

        $this->authChecker = $this
            ->getMockBuilder('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface')
            ->getMock()
        ;

        $this->filter = new NodeFilter($this->tokenStorage, $this->authChecker);

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

        $this->tokenStorage
            ->expects($this->any())
            ->method('getToken')
            ->will($this->returnValue($getTokenReturn))
        ;

        $this->authChecker
            ->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue($isGrantedReturn))
        ;

        if ($expectsFiltered) {
            $this->parent->expects($this->once())->method('removeChild');
        } else {
            $this->parent->expects($this->never())->method('removeChild');
        }

        $return = $this->filter->visit($this->node);

        if ($expectsFiltered) {
            $this->assertSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        } else {
            $this->assertNotSame(MenuTreeTraverserInterface::STOP_TRAVERSAL, $return);
        }
    }

    public function getTestData()
    {
        return array(
            array(array(), true, true, false),
            array(array('FOO'), true, true, false),
            array(array('FOO'), true, false, true),
            array(array('FOO'), false, true, true),
        );
    }
}
