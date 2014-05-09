<?php

use TalkTalk\Model\User as TalkTalkUser;
use TalkTalk\CorePlugins\PhpBb\Model\User as PhpBbUser;

$app['phpbb.import.import-users'] = $app->protect(
    function ($nbToImport = 100, $from = 0) use ($app) {

        $phpBbUsers =
            PhpBbUser::whereIn('user_type', array(PhpBbUser::TYPE_USER, PhpBbUser::TYPE_ADMIN))
                ->orderBy('user_id')
                ->skip($from)->take($nbToImport)
                ->get(array('username', 'user_password', 'user_email'));

        $nbUsersCreated = 0;
        foreach ($phpBbUsers as $phpBbUser) {

            $talkTalkUser = new TalkTalkUser();
            $talkTalkUser->login = $phpBbUser->username;
            $talkTalkUser->email = $phpBbUser->user_email;
            $talkTalkUser->password = $phpBbUser->user_password;
            $talkTalkUser->provider = 'phpbb-import';
            $talkTalkUser->save();
            $nbUsersCreated++;

        }

        return $nbUsersCreated;
    }
);