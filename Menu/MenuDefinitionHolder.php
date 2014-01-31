<?php
namespace DM\MenuBundle\Menu;


class MenuDefinitionHolder {

    /**
     * @var array
     */
    protected $definitions = array();

    /**
     * @param $name
     * @param $config
     */
    public function addMenuDefinition($name, $config) {
        $this->definitions[$name] = $config;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getMenuDefinition($name) {
        if(!isset($this->definitions[$name])) {
            throw new \InvalidArgumentException("No definition for menu '{$name}' found!");
        }

        return $this->definitions[$name];
    }

} 