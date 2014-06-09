<?php

namespace TalkTalk\CorePlugins\PhpBb\Model;

class User extends BaseModel
{

    const TYPE_USER = 0;
    const TYPE_BOT = 1;
    const TYPE_ADMIN = 3;

    protected $table = 'users';

    public function scopeRealUsers($query)
    {
        return $query->whereIn('user_type', array(self::TYPE_USER, self::TYPE_ADMIN));
    }

}
