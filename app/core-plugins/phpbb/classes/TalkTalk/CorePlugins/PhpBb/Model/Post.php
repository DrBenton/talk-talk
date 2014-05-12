<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{

    protected $connection = 'phpbb';

    protected $table = 'posts';

}
