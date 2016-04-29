<?php

namespace DM\MenuBundle\Twig;

use DM\MenuBundle\Menu\MenuFactoryInterface;
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

    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'dm_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('dm_menu_section_label', array($this, 'getMenuSectionLabel')),
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
            'nested' => true
        );

        $template = isset($options['template']) ?
            $this->loadTemplate($options['template']) :
            $this->loadTemplateFromMenuConfig($name)
        ;

        unset($options['template']);
        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        return $template->renderBlock('render_root', $finalOptions);
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
        $menu = $this->menuFactory->create($name);
        $activeChild = $menu ? $menu->getFirstActiveChild() : null;

        // even if menu exists, it may not have an active node
        return null === $activeChild ? '' : $menu->getFirstActiveChild()->getLabel();
    }

    /**
     * @param string $templateName
     *
     * @return Twig_TemplateInterface
     */
    protected function loadTemplate($templateName)
    {
        return $this->twig->loadTemplate($templateName);
    }

    /**
     * @param $name
     *
     * @return \Twig_TemplateInterface
     */
    protected function loadTemplateFromMenuConfig($name)
    {
        $menuConfig = $this->menuConfigProvider->getMenuConfig($name);

        return $this->loadTemplate($menuConfig['twig_template']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'dm_menu_extension';
    }
}
