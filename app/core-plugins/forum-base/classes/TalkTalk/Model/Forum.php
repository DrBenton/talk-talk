<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{

    protected static $instances = array();
    protected $children = array();
    protected $parent = null;
    protected $hasChild = false;
    protected $hasParent = false;

    /**
     * @return array
     */
    public static function getInstances()
    {
        return self::$instances;
    }

    /**
     * @param $id
     * @return \TalkTalk\Model\Forum|null
     */
    public static function getInstance($id)
    {
        foreach (self::$instances as $instance) {
            if ($instance->id === $id) {
                return $instance;
            }
        }

        return null;
    }

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        self::$instances[] = & $this;
    }

    public function addChild(Forum $childForum)
    {
        $this->children[] = $childForum;
        $this->hasChild = true;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function hasChild()
    {
        return $this->hasChild;
    }

    public function setParent(Forum $parentForum)
    {
        $this->parent = $parentForum;
        $this->hasParent = true;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function hasParent()
    {
        return $this->hasParent;
    }

}
