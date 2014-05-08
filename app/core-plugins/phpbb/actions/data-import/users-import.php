<?php

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\User as TalkTalkUser;
use TalkTalk\CorePlugins\PhpBb\Model\User as PhpBbUser;

$action = function (Application $app, Request $request) {

    $phpBbUsers = PhpBbUser::whereIn('user_type', array(3, 0))
        ->get(array('username', 'user_password', 'user_email'))->take(2);
    foreach ($phpBbUsers as $phpBbUser) {
        $talkTalkUser = new TalkTalkUser();
        $talkTalkUser->login = $phpBbUser->username;
        $talkTalkUser->email = $phpBbUser->user_email;
        $talkTalkUser->password = $phpBbUser->user_password;
        $talkTalkUser->provider = 'phpbb-import';
        print_r($talkTalkUser);
        $talkTalkUser->save();
    }

    return 'done!';

};

return $action;
