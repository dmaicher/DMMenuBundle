<?php
namespace DM\MenuBundle\Tests\Menu;


use DM\MenuBundle\Menu\MenuDefinitionHolder;

class MenuDefinitionHolderTest extends \PHPUnit_Framework_TestCase {

    public function testAddMenuDefinitionAndGetMenuDefinition()
    {
        $holder = new MenuDefinitionHolder();

        $holder->addMenuDefinition('name', array('some_config_var'));

        $this->assertSame(array('some_config_var'), $holder->getMenuDefinition('name'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetMenuDefinitionThrowsException()
    {
        $holder = new MenuDefinitionHolder();
        $holder->getMenuDefinition('some_not_existing_name');
    }

} 