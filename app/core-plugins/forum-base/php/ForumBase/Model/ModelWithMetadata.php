<?php

namespace TalkTalk\CorePlugin\ForumBase\Model;

use Illuminate\Database\Eloquent\Model;

class ModelWithMetadata extends Model
{
    protected function getMetadataAttribute($value)
    {
        return json_decode($value, true);
    }

    protected function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = json_encode($value);
    }
}
