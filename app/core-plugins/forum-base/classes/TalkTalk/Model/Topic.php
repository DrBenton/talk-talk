<?php

namespace TalkTalk\Model;

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
     * @return \TalkTalk\Model\Post
     */
    public function firstPost()
    {
        static $firstPost;

        if (!isset($firstPost)) {
            $firstPost = Post::with('author')
                ->where('topic_id', '=', $this->id)
                ->orderBy('created_at', 'ASC')
                ->take(1)
                ->first();
        }

        return $firstPost;
    }

    /**
     * @return \TalkTalk\Model\Post
     */
    public function lastPost()
    {
        static $lastPost;

        if (!isset($lastPost)) {
            $lastPost = Post::with('author')
                ->where('topic_id', '=', $this->id)
                ->orderBy('created_at', 'DESC')
                ->take(1)
                ->first();
        }

        return $lastPost;
    }

    /**
     * @param  int   $nbPosts
     * @return array an array of \TalkTalk\Model\Post instances
     */
    public function lastPosts($nbPosts)
    {
        return Post::with('author')
            ->where('topic_id', '=', $this->id)
            ->orderBy('created_at', 'DESC')
            ->take($nbPosts)
            ->get()
            ->all();
    }

}
