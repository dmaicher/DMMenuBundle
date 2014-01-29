<?php
namespace DM\MenuBundle\Menu;

interface MenuBuilder {
    public function buildMenu(Node $root);
} 