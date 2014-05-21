<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

    public function forum()
    {
        return $this->belongsTo('TalkTalk\Model\Forum');
    }

    public function posts()
    {
        return $this->hasMany('TalkTalk\Model\Post');
    }

}
