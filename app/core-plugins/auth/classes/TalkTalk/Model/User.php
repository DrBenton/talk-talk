<?php

namespace TalkTalk\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

    protected $fillable = array('login', 'email');

    protected $hidden = array('password');

    protected $attributes = array(
        'login' => '',
        'email' => '',
    );

}
