<?php

use TalkTalk\Model\User as TalkTalkUser;
use TalkTalk\CorePlugins\PhpBb\Model\User as PhpBbUser;

$app->vars['phpbb.import.users.nb_items_per_batch'] = 100;

$app->defineFunction(
    'phpbb.import.users.metadata',
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app->vars['phpbb.import.users.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbUser::realUsers()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app->defineFunction(
    'phpbb.import.users.trigger_batch',
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
        // TODO: handle it somewhere else than in a Session
        $idsMapping = $app->get('session')->get('phpbb.import.users.ids_mapping', array());

        $nbUsersCreated = 0;

        foreach ($phpBbUsers as $phpBbUser) {

            $talkTalkUser = new TalkTalkUser();
            $talkTalkUser->login = $phpBbUser->username;
            $talkTalkUser->email = $phpBbUser->user_email;
            $talkTalkUser->password = $phpBbUser->user_password;
            $talkTalkUser->setCreatedAt($phpBbUser->user_regdate);
            $talkTalkUser->setUpdatedAt($phpBbUser->user_lastmark);
            $app->exec('phpbb.import.add_provider_data', $talkTalkUser, $phpBbUser->user_id);
            $talkTalkUser->save();

            $idsMapping[$phpBbUser->user_id] = $talkTalkUser->id;

            $nbUsersCreated++;

        }

        // We have to keep the users ids in our Session data, as other phpBb import processes will need it
        $app->get('session')->set('phpbb.import.users.ids_mapping', $idsMapping);

        return $nbUsersCreated;
    }
);