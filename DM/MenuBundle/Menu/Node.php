<?php
namespace DM\MenuBundle\Menu;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class Node {

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var Node
     */
    protected $parent;

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @var array
     */
    protected $activeRoutes = array();

    /**
     * @var array
     */
    protected $activeUrls = array();

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
     * @var array
     */
    protected $requiredRoles = array();

    /**
     * @var boolean
     */
    protected $hasVisibleChildren = false;

    /**
     * @var array
     */
    protected $activeChildren = array();

    /**
     * @var array
     */
    protected $viewAttributes = array();

    /**
     * @param $label
     * @param $url
     * @param array $routes
     */
    public function __construct($label, $url)
    {
        $this->label = $label;
        $this->url = $url;
        $this->activeUrls[] = $url;
    }

    /**
     * @param Request $request
     */
    public function update(Request $request, SecurityContext $securityContext)
    {
        if($this->parent) {
            foreach($this->requiredRoles as $role) {
                if(!$securityContext->getToken() || !$securityContext->isGranted($role)) {
                    $this->setVisible(false);
                    return; //no further updates required for this branch of the menu tree as its invisible anyway
                }
            }

            $requestUri = $request->getUri();
            $requestPath = $request->getPathInfo();

            foreach($this->activeUrls as $url) {
                if($requestUri == $url || $requestPath == $url) {
                    $this->setCurrent(true);
                    break;
                }
            }

            if(!$this->current) {
                $requestRoute = $request->get('_route');
                foreach($this->activeRoutes as $route) {
                    if($route == $requestRoute) {
                        $this->setCurrent(true);
                        break;
                    }
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
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
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
     * @return Node
     */
    public static function create($label, $url)
    {
        return new Node($label, $url);
    }

    /**
     * @param array $activeRoutes
     */
    public function setActiveRoutes($activeRoutes)
    {
        $this->activeRoutes = $activeRoutes;

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveRoutes()
    {
        return $this->activeRoutes;
    }

    /**
     * @param array $activeUrls
     */
    public function setActiveUrls($activeUrls)
    {
        $this->activeUrls = $activeUrls;

        return $this;
    }

    /**
     * @return array
     */
    public function getActiveUrls()
    {
        return $this->activeUrls;
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
            if($this->url !== null) {
                $this->parent->propagateUrl($this->url, $this->current);
            }
        }
    }

    /**
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible && ($this->url !== null || $this->hasVisibleChildren);
    }

    /**
     * @param array $requiredRoles
     */
    public function setRequiredRoles($requiredRoles)
    {
        $this->requiredRoles = $requiredRoles;

        return $this;
    }

    /**
     * @return array
     */
    public function getRequiredRoles()
    {
        return $this->requiredRoles;
    }

    /**
     * @param \DM\MenuBundle\Menu\Node $firstVisibleChild
     */
    protected function propagateUrl($url, $current)
    {
        if($this->url === null) {
            $this->url = $url;
            if($current) {
                $this->setCurrent(true);
            }
        }

        if($this->parent) {
            $this->parent->propagateUrl($url, $this->current);
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
     * @param array $viewAttributes
     */
    public function setViewAttributes($viewAttributes)
    {
        $this->viewAttributes = $viewAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getViewAttributes()
    {
        return $this->viewAttributes;
    }
} 