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
                ->get(array(
                    'user_id', 'username', 'user_password', 'user_email',
                    'user_regdate', 'user_lastmark'
                ));

        // This array will allow us to map phpBb users ids to our new Talk-Talk users ids
        // This is a heavy var to store in Session, it will be useful for next imports processes
        // and we will carefully clear this var after the phpbb import process.
        $idsMapping = $app['session']->get('phpbb.import.users.ids_mapping', array());

        $nbUsersCreated = 0;

        foreach ($phpBbUsers as $phpBbUser) {

            $talkTalkUser = new TalkTalkUser();
            $talkTalkUser->login = $phpBbUser->username;
            $talkTalkUser->email = $phpBbUser->user_email;
            $talkTalkUser->password = $phpBbUser->user_password;
            $talkTalkUser->provider = 'phpbb-import';
            $talkTalkUser->setCreatedAt($phpBbUser->user_regdate);
            $talkTalkUser->setUpdatedAt($phpBbUser->user_lastmark);
            $talkTalkUser->save();

            $idsMapping[$phpBbUser->user_id] = $talkTalkUser->id;

            $nbUsersCreated++;

        }

        // We have to keep the users ids in our Session data, as other phpBb import processes will need it
        $app['session']->set('phpbb.import.users.ids_mapping', $idsMapping);

        return $nbUsersCreated;
    }
);
