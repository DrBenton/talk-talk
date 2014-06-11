<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class Forum
 *
 * As we may have deep parent/children relations in this table and not so many
 * rows in the DB table, we manage specific static "instances" properties, and we override
 * the Illuminate Model "all", "find" and "findOrFail" static methods so that they use this static props.
 *
 * //TODO: override other Illuminate DB static methods, like "firstByAttributes"
 *
 * @package TalkTalk\Model
 */
class Forum extends ModelWithMetadata
{

    const CACHE_KEY = 'talk-talk/forum-base/model/forum';
    const CACHE_LIFETIME = 120;

    protected static $flatInstances = null;
    protected static $treeInstances = null;
    protected $children = array();
    protected $parent = null;
    protected $hasChild = false;
    protected $hasParent = false;

    protected $guarded = array();

    /**
     * @return array
     */
    public static function all($columns = array('*'))
    {
        static::initTree();

        return array_values(static::$flatInstances);
    }

    /**
     * Find a model by its primary key.
     *
     * @param  mixed                                                 $id
     * @return \Illuminate\Database\Eloquent\Model|Collection|static
     */
    public static function find($id, $columns = array('*'))
    {
        static::initTree();

        if (!isset(static::$flatInstances[$id])) {
            return null;
        }

        return static::$flatInstances[$id];
    }

    /**
     * Find a model by its primary key or throw an exception.
     *
     * @param  mixed                                                 $id
     * @return \Illuminate\Database\Eloquent\Model|Collection|static
     */
    public static function findOrFail($id, $columns = array('*'))
    {
        static::initTree();

        $model = static::find($id);

        if (null !== $model) {
            return $model;
        }

        throw new ModelNotFoundException(sprintf('Could not find Forum "%d"!', $id));
    }

    protected static function initTree()
    {
        if (null !== static::$treeInstances) {
            return; //the Forums tree has already been populated
        }

        $allForums = static::getAllForums();

        // Flat instances array
        static::$flatInstances = array();
        foreach ($allForums as $forum) {
            static::$flatInstances[$forum->id] = $forum;
        }

        // Tree instances array
        static::$treeInstances = array();
        foreach ($allForums as $forum) {
            if (null === $forum->parent_id) {
                // This forum has no parent: we just have to add it to the root forums array
                static::$treeInstances[] = $forum;
            } else {
                // We have to find this forum parent!
                $parentForum = static::findOrFail($forum->parent_id);
                $parentForum->addChild($forum);
                $forum->setParent($parentForum);
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected static function getAllForums()
    {
        $dummyInstance = new static;
        $cacheManager = $dummyInstance->getConnection()->getCacheManager();

        $cachedData = $cacheManager->get(static::CACHE_KEY);

        if (null !== $cachedData) {

            // We restore all this data from cache!
            $allForumsRaw = & $cachedData;
            $allForums = new Collection();
            foreach ($allForumsRaw as $forumRaw) {
                foreach ($forumRaw as $key => $value) {
                    if (preg_match('~^-?\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$~', $value)) {
                        $forumRaw[$key] = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
                    }
                }
                $allForums->add(new static($forumRaw));
            }

        } else {

            $allForums = static::query()
                ->orderBy('parent_id')
                ->orderBy('id')
                ->get();

            // Cache management: we won't have to do all this stuff again
            $allForumsRaw = array();
            foreach ($allForums as $forum) {
                $allForumsRaw[] = $forum->attributesToArray();
            }
            $cacheManager->put(static::CACHE_KEY, $allForumsRaw, static::CACHE_LIFETIME / 60);
        }

        return $allForums;
    }

    /**
     * @return array
     */
    public static function getTree()
    {
        static::initTree();

        return static::$treeInstances;
    }

    public function topics()
    {
        return $this->hasMany('TalkTalk\Model\Topic');
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

    public function getParents()
    {
        $res = array();

        if (!$this->hasParent()) {
            return $res;
        }

        $forum = $this;
        while ($forum->hasParent()) {
            $parentForum = $forum->getParent();
            $res[] = $parentForum;
            $forum = $parentForum;
        }

        return $res;
    }

    public function hasParent()
    {
        return $this->hasParent;
    }

    public function depth()
    {
        return count($this->getParents());
    }

}
