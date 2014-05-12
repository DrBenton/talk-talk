<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{

    protected $connection = 'phpbb';

    protected $table = 'topics';

}
