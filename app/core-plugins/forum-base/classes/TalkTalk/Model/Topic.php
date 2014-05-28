<?php

namespace TalkTalk\Model;

class Topic extends ModelWithMetadata
{

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
            $firstPost = Post::where('topic_id', '=', $this->id)
                ->orderBy('created_at', 'ASC')
                ->take(1)
                ->first()
                ->load('author');
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
            $lastPost = Post::where('topic_id', '=', $this->id)
                ->orderBy('created_at', 'DESC')
                ->take(1)
                ->first()
                ->load('author');
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
