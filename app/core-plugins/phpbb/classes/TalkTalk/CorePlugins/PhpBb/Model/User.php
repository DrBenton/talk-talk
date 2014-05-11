<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    const TYPE_USER = 0;
    const TYPE_BOT = 1;
    const TYPE_ADMIN = 3;

    protected $connection = 'phpbb';

    protected $table = 'users';

}
