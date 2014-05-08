<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $connection = 'phpbb';

    protected $table = 'users';

}
