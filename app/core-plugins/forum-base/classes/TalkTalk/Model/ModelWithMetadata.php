<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class ModelWithMetadata extends Model
{
    protected function getMetadataAttribute($value)
    {
//        if ($this->attributes['id'] === 7)
//            die('$value='.$value."\n".print_r($this->attributes, true));
        return json_decode($value, true);
    }

    protected function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = json_encode($value);
    }
}
