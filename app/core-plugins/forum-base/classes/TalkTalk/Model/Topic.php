<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;
use TalkTalk\Model\Forum;

class Topic extends Model
{

    public function forum()
    {
        return Forum::find($this->forum_id);
    }

    public function author()
    {
        return $this->belongsTo('TalkTalk\Model\User');
    }

    public function posts()
    {
        return $this->hasMany('TalkTalk\Model\Post');
    }

}
