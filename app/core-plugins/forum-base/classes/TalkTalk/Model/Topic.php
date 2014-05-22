<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
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

    public function lastReply()
    {
        static $lastReply;

        if (!isset($lastReply)) {
            $lastReply = Post::where('topic_id', '=', $this->id)
                ->orderBy('created_at', 'DESC')
                ->take(1)
                ->first()
                ->load('author');
        }

        return $lastReply;
    }

}
