<?php

namespace TalkTalk\Model;

class Post extends ModelWithMetadata
{

    public function topic()
    {
        return $this->belongsTo('TalkTalk\Model\Topic');
    }

    public function author()
    {
        return $this->belongsTo('TalkTalk\Model\User', 'author_id');
    }

}
