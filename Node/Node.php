<?php
namespace DM\MenuBundle\Node;

class Node {

    /**
     * @var int
     */
    private static $counter = 0;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParams = array();

    /**
     * @var array
     */
    protected $additionalActiveRoutes = array();

    /**
     * @var array
     */
    protected $requiredRoles = array();

    /**
     * @var array
     */
    protected $attr = array();

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
     * @param $label
     */
    public function __construct($label = null)
    {
        $this->label = $label;
        $this->id = self::$counter++;
    }

    /**
     * @param Node $parent
     * @return $this
     */
    public function setParent(Node $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Node
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Node $child
     * @return $this
     */
    public function addChild(Node $child)
    {
        $this->children[$child->getId()] = $child;
        $child->setParent($this);

        return $this;
    }

    /**
     * @param Node $child
     */
    public function removeChild(Node $child)
    {
        if(isset($this->children[$child->getId()])) {
            unset($this->children[$child->getId()]);
            $child->setParent(null);
        }
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
     * @param $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        if($this->parent && $active) {
            $this->parent->setActive(true);
        }

        return $this;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * returns the first active child
     * @return Node|null
     */
    public function getFirstActiveChild()
    {
        if($this->active) {
            foreach($this->children as $child) {
                if($child->isActive()) {
                    return $child;
                }
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param array $additionalActiveRoutes
     * @return $this
     */
    public function setAdditionalActiveRoutes(array $additionalActiveRoutes)
    {
        $this->additionalActiveRoutes = $additionalActiveRoutes;

        return $this;
    }

    /**
     * @return array
     */
    public function getAdditionalActiveRoutes()
    {
        return $this->additionalActiveRoutes;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function setAttr($key, $value)
    {
        $this->attr[$key] = $value;

        return  $this;
    }

    /**
     * @param $key
     * @return null
     */
    public function getAttr($key)
    {
        if(isset($this->attr[$key])) {
            return $this->attr[$key];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attr;
    }

    /**
     * @param $label
     * @return $this
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
     * @param array $requiredRoles
     * @return $this
     */
    public function setRequiredRoles(array $requiredRoles)
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
     * @param string $route
     * @return $this
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @param array $routeParams
     * @return $this
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * @return array
     */
    public function getRouteParams()
    {
        return $this->routeParams;
    }

    /**
     * @return bool
     */
    public function isRootNode()
    {
        return $this->parent === null;
    }

    /**
     * @return bool
     */
    public function hasRoute()
    {
        return $this->getRoute() !== null;
    }

    /**
     * @return array
     */
    public function getAllActiveRoutes()
    {
        return array_merge(array($this->getRoute()), $this->getAdditionalActiveRoutes());
    }

    /**
     * @return bool
     */
    public function isFirstChild()
    {
        if($this->parent) {
            $children = $this->parent->getChildren();
            return $this === reset($children);
        }

        return false;
    }

    /**
     * returns first child node with route
     *
     * @return $this|null
     */
    public function getFirstChildWithRoute()
    {
        foreach($this->children as $child) {
            if($child->hasRoute()) {
                return $child;
            }
        }

        return null;
    }
} 