<?php
namespace DM\MenuBundle\Twig;


use DM\MenuBundle\Menu\MenuDefinitionHolder;
use DM\MenuBundle\Menu\MenuFactoryInterface;
use DM\MenuBundle\Menu\Node;

class MenuExtension extends \Twig_Extension {

    /**
     * @var MenuFactoryInterface
     */
    private $menuFactory;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var MenuDefinitionHolder
     */
    protected $menuDefinitionHolder;

    public function __construct(
        MenuFactoryInterface $menuFactory, \Twig_Environment $twig,
        MenuDefinitionHolder $menuDefinitionHolder
    ){
        $this->menuFactory = $menuFactory;
        $this->twig = $twig;
        $this->menuDefinitionHolder = $menuDefinitionHolder;
    }

    public function getFunctions()
    {
        return array(
            'dm_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html')))
        );
    }

    /**
     * @param $name
     * @param array $options
     * @return mixed
     */
    public function render($name, array $options = array())
    {
        $menu = $this->menuFactory->create($name);

        $defaultOptions = array(
            'collapse' => false,
            'nested' => true
        );
        
        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        return $this->getTemplate($name)->renderBlock('render_root', $finalOptions);
    }

    /**
     * @param $name
     * @return \Twig_TemplateInterface
     */
    protected function getTemplate($name)
    {
        $menuDefinition = $this->menuDefinitionHolder->getMenuDefinition($name);

        return $this->twig->loadTemplate($menuDefinition['twig_template']);
    }

    public function getName()
    {
        return 'dm_menu_extension';
    }
} 