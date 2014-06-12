<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Topic extends ModelWithMetadata
{

    protected $fillable = array('title', 'content');

    protected $attributes = array(
        'title' => '',
        'content' => '',
    );

    /**
     * @inheritdoc
     */
    public function save(array $options = array())
    {
        // Our "content" attribute is virtual: in fact, it is used to create a new Post.
        // --> let's unset this attribute before save!
        unset($this->attributes['content']);

        parent::save($options);
    }

    public function forum()
    {
        return Forum::find($this->forum_id);
    }

    public function author()
    {
        return $this->belongsTo('TalkTalk\Model\User', 'author_id');
    }

    public function posts()
    {
        return $this->hasMany('TalkTalk\Model\Post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function firstPost()
    {
        static $firstPost;//poor man's cache

        if (!isset($firstPost)) {
            $post = new Post();
            $firstPostQuery = $post->query()
                ->with('author')
                ->orderBy('created_at', 'ASC')
                ->take(1);
            $firstPost = new HasOne($firstPostQuery, $this, $post->getTable().'.topic_id', 'id');
        }

        return $firstPost;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function lastPost()
    {
        static $lastPost;//poor man's cache

        if (!isset($lastPost)) {
            $post = new Post();
            $lastPostQuery = $post->query()
                ->with('author')
                ->orderBy('created_at', 'DESC')
                ->take(1);
            $lastPost = new HasOne($lastPostQuery, $this, $post->getTable().'.topic_id', 'id');
        }

        return $lastPost;
    }

    /**
     * @param  int                                              $nbPosts
     * @return \Illuminate\Database\Eloquent\Relations\Relation
     */
    public function lastPosts($nbPosts)
    {
        $post = new Post();
        $lastPostsQuery = $post->query()
            ->with('author')
            ->orderBy('created_at', 'DESC')
            ->take($nbPosts);
        $lastPosts = new HasMany($lastPostsQuery, $this, $post->getTable().'.topic_id', 'id');

        return $lastPosts;
    }

}
