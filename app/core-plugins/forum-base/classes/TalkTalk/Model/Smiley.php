<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Smiley extends Model
{

    public function scopeByRank($query)
    {
        return $query->orderBy('rank', 'ASC');
    }

}
