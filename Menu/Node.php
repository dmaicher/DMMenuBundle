<?php
namespace DM\MenuBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class Node {

    protected $options = array(
        'label' => null,
        'route' => null,
        'route_params' => array(),
        'additional_active_routes' => array(),
        'required_roles' => array(),
        'attr' => array()
    );

    /**
     * @var Node
     */
    protected $parent;

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @var boolean
     */
    protected $active = false;

    /**
     * @var boolean
     */
    protected $current = false;

    /**
     * @var boolean
     */
    protected $visible = true;

    /**
     * @var boolean
     */
    protected $hasVisibleChildren = false;

    /**
     * @var array
     */
    protected $activeChildren = array();
    
    /**
     * @var boolean
     */
    protected $isFirstChild = false;

    public function __construct(array $options = array())
    {
        $invalidOptions = array_diff_key($options, $this->options);

        if(count($invalidOptions) > 0) {
            throw new \InvalidArgumentException("unknown option(s): ".implode($invalidOptions, ', '));
        }

        $this->options = array_merge($this->options, $options);

        if($this->options['route']) {
            $this->options['additional_active_routes'][] = $this->options['route'];
        }
    }

    /**
     * @param Request $request
     */
    public function update(Request $request, SecurityContext $securityContext)
    {
        if($this->parent) {
            foreach($this->options['required_roles'] as $role) {
                if(!$securityContext->getToken() || !$securityContext->isGranted($role)) {
                    $this->setVisible(false);
                    return; //no further updates required for this branch of the menu tree as its invisible anyway
                }
            }

            $requestRoute = $request->get('_route');
            foreach($this->options['additional_active_routes'] as $route) {
                if($route == $requestRoute) {
                    $this->setCurrent(true);
                    break;
                }
            }
        }

        $this->setVisible(true);

        foreach($this->getChildren() as $child) {
            $child->update($request, $securityContext);
        }
    }

    /**
     * @param boolean $active
     */
    protected function setActive($active)
    {
        $this->active = $active;

        if($active && $this->parent) {
            $this->parent->setActive(true);
            $this->parent->addActiveChild($this);
        }
    }

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param \DM\MenuBundle\Menu\Node $parent
     */
    protected function setParent(Node $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return \DM\MenuBundle\Menu\Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Node $child
     */
    public function addChild(Node $child)
    {
        if(count($this->children) == 0) {
            $child->setIsFirstChild(true);
        }
        
        $this->children[] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * returns the layer of this node in the menu tree.
     * Root node has layer 0. So for actual menu nodes the layer starts with 1.
     * @return int
     */
    public function getLayer()
    {
        if($this->parent) {
            return $this->parent->getLayer() + 1;
        }

        return 0; //root
    }

    /**
     * @param boolean $current
     */
    protected function setCurrent($current)
    {
        $this->current = $current;

        if($current) {
            $this->setActive(true);
        }
    }

    /**
     * @return boolean
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * @param boolean $visible
     */
    protected function setVisible($visible)
    {
        $this->visible = $visible;
        if($visible && $this->parent) {
            $this->parent->setHasVisibleChildren(true);
            if($this->options['route'] !== null) {
                $this->parent->propagateRoute($this->options['route'], $this->options['route_params'], $this->current);
            }
        }
    }

    /**
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible && ($this->options['route'] !== null || $this->hasVisibleChildren);
    }

    protected function propagateRoute($route, array $routeParams, $current)
    {
        if($this->options['route'] === null) {
            $this->options['route'] = $route;
            $this->options['route_params'] = $routeParams;
            if($current) {
                $this->setCurrent(true);
            }
        }

        if($this->parent) {
            $this->parent->propagateRoute($route, $routeParams, $this->current);
        }
    }

    /**
     * @return bool
     */
    public function hasVisibleChildren()
    {
        return $this->hasVisibleChildren;
    }

    /**
     * @param boolean $hasVisibleChildren
     */
    protected function setHasVisibleChildren($hasVisibleChildren)
    {
        $this->hasVisibleChildren = $hasVisibleChildren;
    }

    /**
     * @param \DM\MenuBundle\Menu\Node $activeChild
     */
    protected function addActiveChild($activeChild)
    {
        $this->activeChildren[] = $activeChild;
    }

    /**
     * @return array
     */
    public function getActiveChildren()
    {
        return $this->activeChildren;
    }
    
    /**
     * @param boolean $isFirstChild
     */
    public function setIsFirstChild($isFirstChild)
    {
        $this->isFirstChild = $isFirstChild;
    }
    
    /**
     * @return boolean
     */
    public function isFirstChild()
    {
        return $this->isFirstChild;
    }

    /**
     * @param $key
     * @param null $default
     * @return null
     */
    public function get($key, $default = null)
    {
        if(isset($this->options[$key])){
            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @param null $label
     * @param array $options
     * @return Node
     */
    public static function create($label = null, array $options = array())
    {
        $options['label'] = $label;
        return new Node($options);
    }
} 