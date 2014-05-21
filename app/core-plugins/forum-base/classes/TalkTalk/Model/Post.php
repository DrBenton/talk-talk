<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    public function topic()
    {
        return $this->belongsTo('TalkTalk\Model\Topic');
    }

    public function author()
    {
        return $this->belongsTo('TalkTalk\Model\User');
    }

}
