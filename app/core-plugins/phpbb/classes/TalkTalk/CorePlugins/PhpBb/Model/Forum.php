<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{

    protected $connection = 'phpbb';

    protected $table = 'forums';

}
