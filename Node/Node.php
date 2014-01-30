<?php
namespace DM\MenuBundle\Node;

class Node {

    /**
     * @var int
     */
    private static $counter = 0;

    /**
     * @var array
     */
    protected $options = array(
        'label' => null,
        'route' => null,
        'route_params' => array(),
        'additional_active_routes' => array(),
        'required_roles' => array(),
        'attr' => array()
    );

    /**
     * @var int
     */
    protected $id;

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
     * 
     * @param array $options
     * @throws \InvalidArgumentException
     */
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
     * @return Node|null
     */
    public function getActiveChild()
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
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
        return $this->get('route') !== null;
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
     * @return array|null
     */
    protected function getRouteAndParamsOrNull()
    {
        if($this->get('route') !== null) {
            return array(
                'route' => $this->get('route'),
                'route_params' => $this->get('route_params'),
            );
        }

        return null;
    }

    /**
     * will check this node and its direct children nodes and return first route information that is found
     *
     * array('route' => '...', 'route_params' => array(...))
     *
     * @return array|null
     */
    public function getFirstRouteAndParams()
    {
        if($routeInfo = $this->getRouteAndParamsOrNull()) {
            return $routeInfo;
        }

        foreach($this->children as $child) {
            if($routeInfo = $child->getRouteAndParamsOrNull()) {
                return $routeInfo;
            }
        }

        return null;
    }
} 