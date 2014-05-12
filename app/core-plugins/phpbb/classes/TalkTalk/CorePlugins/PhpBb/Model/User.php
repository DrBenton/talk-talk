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

    public function scopeRealUsers($query)
    {
        return $query->whereIn('user_type', array(self::TYPE_USER, self::TYPE_ADMIN));
    }

}
