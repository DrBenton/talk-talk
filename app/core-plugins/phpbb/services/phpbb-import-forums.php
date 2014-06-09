<?php

use TalkTalk\Model\Forum as TalkTalkForum;
use TalkTalk\CorePlugins\PhpBb\Model\Forum as PhpBbForum;

$app->vars['phpbb.import.forums.nb_items_per_batch'] = 100;

$app->defineFunction(
    'phpbb.import.forums.metadata',
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app->vars['phpbb.import.forums.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbForum::query()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app->defineFunction(
    'phpbb.import.forums.trigger_batch',
    function ($nbToImport, $from) use ($app) {

        $phpBbForums =
            PhpBbForum::query()
                ->orderBy('parent_id')
                ->skip($from)->take($nbToImport)
                ->get(array(
                    'forum_id', 'parent_id', 'forum_name', 'forum_desc', 'forum_posts', 'forum_topics',
                    'forum_last_post_time'
                ));

        // This array will allow us to map phpBb forums ids to our new Talk-Talk forums ids
        $idsMapping = $app->get('session')->get('phpbb.import.forums.ids_mapping', array());

        $nbForumsCreated = 0;

        foreach ($phpBbForums as $phpBbForum) {

            if (!$phpBbForum->parent_id) {
                $talkTalkParentId = null;
            } else {
                $talkTalkParentId = $idsMapping[$phpBbForum->parent_id];
            }

            $talkTalkForum = new TalkTalkForum();
            $talkTalkForum->parent_id = $talkTalkParentId;
            $talkTalkForum->title = html_entity_decode($phpBbForum->forum_name);
            $talkTalkForum->desc = html_entity_decode($phpBbForum->forum_desc);
            $talkTalkForum->nb_posts = $phpBbForum->forum_posts;
            $talkTalkForum->nb_topics = $phpBbForum->forum_topics;
            // No creation date for Forums in phpBb... :-/
            // We just have the following "last post time":
            $talkTalkForum->setUpdatedAt($phpBbForum->forum_last_post_time);
            $app->exec('phpbb.import.add_provider_data', $talkTalkForum, $phpBbForum->forum_id);
            $talkTalkForum->save();

            $idsMapping[$phpBbForum->forum_id] = $talkTalkForum->id;

            $nbForumsCreated++;

        }

        // We have to keep the forums ids in our Session data, as other phpBb import processes may need it
        $app->get('session')->set('phpbb.import.forums.ids_mapping', $idsMapping);

        return $nbForumsCreated;
    }
);