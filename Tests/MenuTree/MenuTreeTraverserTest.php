<?php
namespace DM\MenuBundle\Tests\MenuTree;


use DM\MenuBundle\MenuTree\MenuTreeTraverser;
use DM\MenuBundle\MenuTree\MenuTreeTraverserInterface;
use DM\MenuBundle\Node\Node;

class MenuTreeTraverserTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var MenuTreeTraverser
     */
    protected $traverser;

    /**
     * @var array
     */
    protected $visitors = array();

    public function setUp()
    {
        $this->traverser = new MenuTreeTraverser();

        $this->visitors[] = $this->getMockBuilder('DM\MenuBundle\NodeVisitor\NodeVisitorInterface')->getMock();
        $this->visitors[] = $this->getMockBuilder('DM\MenuBundle\NodeVisitor\NodeVisitorInterface')->getMock();
        $this->traverser->addVisitor($this->visitors[0]);
        $this->traverser->addVisitor($this->visitors[1]);
    }

    public function testTraverseCallsVisitOnEachVisitor()
    {
        $root = new Node();
        $child = new Node();
        $root->addChild($child);

        foreach($this->visitors as $visitor) {
            $visitor->expects($this->once())->method('visit')->with($child);
        }

        $this->traverser->traverse($root);
    }

    public function testStopTraversal()
    {
        $root = new Node();
        $child = new Node();
        $root->addChild($child);

        $this->visitors[0]
            ->expects($this->once())
            ->method('visit')
            ->with($child)
            ->will($this->returnValue(MenuTreeTraverserInterface::STOP_TRAVERSAL))
        ;

        $this->visitors[1]
            ->expects($this->never())
            ->method('visit')
        ;

        $this->traverser->traverse($root);
    }

    public function testTraverseRecursively()
    {
        $root = new Node();
        $child = new Node();
        $childChild = new Node();
        $child->addChild($childChild);
        $root->addChild($child);

        foreach($this->visitors as $visitor) {
            $visitor->expects($this->exactly(2))->method('visit');
            $visitor->expects($this->exactly(2))->method('visit');
        }

        $this->traverser->traverse($root);
    }
} 