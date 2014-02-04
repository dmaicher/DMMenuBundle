<?php
namespace DM\MenuBundle\MenuTree;

use DM\MenuBundle\Node\Node;

interface MenuTreeBuilderInterface {
    public function build(Node $root);
} 