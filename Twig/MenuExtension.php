<?php

namespace DM\MenuBundle\Twig;

use DM\MenuBundle\Menu\MenuFactoryInterface;
use DM\MenuBundle\Node\Node;
use DM\MenuBundle\MenuConfig\MenuConfigProvider;

class MenuExtension extends \Twig_Extension
{
    /**
     * @var MenuFactoryInterface
     */
    private $menuFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var MenuConfigProvider
     */
    protected $menuConfigProvider;

    public function __construct(
        MenuFactoryInterface $menuFactory, \Twig_Environment $twig,
        MenuConfigProvider $menuConfigProvider
    ) {
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->menuConfigProvider = $menuConfigProvider;
    }

    public function getFunctions()
    {
        return array(
            'dm_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dm_menu_section_label', array($this, 'getMenuSectionLabel')),
            new \Twig_SimpleFunction('dm_menu_first_active_child', array($this, 'getFirstActiveChild')),
        );
    }

    /**
     * @param $name
     * @param array $options
     *
     * @return mixed
     */
    public function render($name, array $options = array())
    {
        $menu = $this->menuFactory->create($name);

        $defaultOptions = array(
            'collapse' => false,
            'nested' => true,
        );

        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        return $this->getTemplate($name)->renderBlock('render_root', $finalOptions);
    }

    /**
     * Get menu section label by name.
     *
     * @param $name
     *
     * @return string
     */
    public function getMenuSectionLabel($name)
    {
        $activeChild = $this->getFirstActiveChild($name);

        return null === $activeChild ? '' : $activeChild->getLabel();
    }

    /**
     * @param string $name
     *
     * @return Node
     */
    public function getFirstActiveChild($name)
    {
        $menu = $this->menuFactory->create($name);

        return $menu ? $menu->getFirstActiveChild() : null;
    }

    /**
     * @param $name
     *
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($name)
    {
        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);

        return $this->twig->loadTemplate($menuConfig['twig_template']);
    }

    public function getName()
    {
        return 'dm_menu_extension';
    }
}
