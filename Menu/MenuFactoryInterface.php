<?php

namespace DM\MenuBundle\Menu;

interface MenuFactoryInterface
{
    /**
     * @param $name
     *
     * @return Menu
     */
    public function create($name);
}
