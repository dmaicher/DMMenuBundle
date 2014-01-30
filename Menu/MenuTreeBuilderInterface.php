<?php
namespace DM\MenuBundle\Menu;

use DM\MenuBundle\Node\Node;
use DM\MenuBundle\Node\NodeFactoryInterface;

interface MenuTreeBuilderInterface {
    public function buildTree(Node $root, NodeFactoryInterface $factory);
} 