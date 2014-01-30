<?php
namespace DM\MenuBundle\Twig;


use DM\MenuBundle\Menu\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuExtension extends \Twig_Extension {

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            'dm_menu_render' => new \Twig_Function_Method($this, 'render', array('is_safe' => array('html')))
        );
    }

    /**
     * @param string $menuBuilderServiceId
     * @return mixed
     */
    public function render($menuBuilderServiceId, array $options = array())
    {
        $menu = $this->buildMenu($menuBuilderServiceId);

        $menu->update($this->get('request'), $this->get('security.context'));
        
        $defaultOptions = array(
            'template' => $this->getDefaultTemplate(),
            'collapse' => false,
            'nested' => true
        );
        
        $finalOptions = array_merge($defaultOptions, $options);
        $finalOptions['currentNode'] = $menu;

        $template = $this->get('twig')->loadTemplate($finalOptions['template']);

        return $template->renderBlock('render_root', $finalOptions);
    }

    public function getName()
    {
        return 'dm_menu_extension';
    }

    /**
     * @return mixed
     */
    protected function getDefaultTemplate()
    {
        return $this->container->getParameter('dm_menu.twig.default_template');
    }

    /**
     * @param string $menuBuilderServiceId
     */
    protected function buildMenu($menuBuilderServiceId)
    {
        $menu = $this->get('dm_menu.root_node_factory')->create();
        $this->get($menuBuilderServiceId)->buildMenu($menu);

        return $menu;
    }

    /**
     * @param string $id
     * @return object
     */
    protected function get($id)
    {
        return $this->container->get($id);
    }
} 