<?php

use TalkTalk\Model\User as TalkTalkUser;
use TalkTalk\CorePlugins\PhpBb\Model\User as PhpBbUser;

$app['phpbb.import.users.nb_items_per_batch'] = 100;

$app['phpbb.import.users.metadata'] = $app->share(
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app['phpbb.import.users.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbUser::realUsers()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);
        
        return $ret;
    }
);

$app['phpbb.import.users.trigger_batch'] = $app->protect(
    function ($nbToImport, $from) use ($app) {

        $phpBbUsers =
            PhpBbUser::realUsers()
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
            
            try {
                $talkTalkUser->save();
            } catch (\Exception $e) {
                if ('23000' === $e->getCode()) {
                    // We tolerate "Duplicate entry" Exceptions, but not others PDOExceptions
                    throw $e;
                }
            }
            
            $nbUsersCreated++;

        }

        return $nbUsersCreated;
    }
);
