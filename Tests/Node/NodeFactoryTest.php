<?php

namespace DM\MenuBundle\Tests\Node;

use DM\MenuBundle\Node\NodeFactory;

class NodeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $factory = new NodeFactory();

        $node = $factory->create('label');

        $this->assertInstanceOf('DM\MenuBundle\Node\Node', $node);
        $this->assertSame('label', $node->getLabel());
    }
}
