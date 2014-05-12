<?php

use TalkTalk\Model\Forum as TalkTalkForum;
use TalkTalk\CorePlugins\PhpBb\Model\Forum as PhpBbForum;

$app['phpbb.import.forums.nb_items_per_batch'] = 100;

$app['phpbb.import.forums.metadata'] = $app->share(
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app['phpbb.import.forums.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbForum::query()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app['phpbb.import.forums.trigger_batch'] = $app->protect(
    function ($nbToImport, $from) use ($app) {

        $phpBbForums =
            PhpBbForum::query()
                ->orderBy('parent_id')
                ->skip($from)->take($nbToImport)
                ->get(array('forum_id', 'parent_id', 'forum_name', 'forum_desc', 'forum_posts', 'forum_topics'));

        // This array will allow us to map phpBb forums ids to our new Talk-Talk forums ids
        $idsMapping = $app['session']->get('phpbb.import.forums.ids_mapping', array());

        $nbForumsCreated = 0;

        foreach ($phpBbForums as $phpBbForum) {

            if (0 === $phpBbForum->parent_id) {
                $talkTalkParentId = null;
            } else {
                $talkTalkParentId = $idsMapping[$phpBbForum->parent_id];
            }

            $talkTalkForum = new TalkTalkForum();
            $talkTalkForum->parent_id = $talkTalkParentId;
            $talkTalkForum->name = $phpBbForum->forum_name;
            $talkTalkForum->desc = $phpBbForum->forum_desc;
            $talkTalkForum->nb_posts = $phpBbForum->forum_posts;
            $talkTalkForum->nb_topics = $phpBbForum->forum_topics;
            $talkTalkForum->save();

            $idsMapping[$phpBbForum->forum_id] = $talkTalkForum->id;

            $nbForumsCreated++;

        }

        // We have to keep the forums ids in our Session data, as other phpBb import processes may need it
        $app['session']->set('phpbb.import.forums.ids_mapping', $idsMapping);

        return $nbForumsCreated;
    }
);
