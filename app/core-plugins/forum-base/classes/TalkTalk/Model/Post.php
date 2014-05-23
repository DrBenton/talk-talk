<?php

namespace TalkTalk\Model;

class Post extends ModelWithMetadata
{

    protected $fillable = array('title', 'content');

    protected $attributes = array(
        'title' => '',
        'content' => '',
    );

    public function topic()
    {
        return $this->belongsTo('TalkTalk\Model\Topic');
    }

    public function author()
    {
        return $this->belongsTo('TalkTalk\Model\User', 'author_id');
    }

}
