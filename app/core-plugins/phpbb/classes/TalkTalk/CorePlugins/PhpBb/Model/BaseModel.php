<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{

    protected $connection = 'phpbb';

}
