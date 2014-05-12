<?php

use TalkTalk\Model\Forum as TalkTalkForum;
use TalkTalk\CorePlugins\PhpBb\Model\Forum as PhpBbForum;

$app['phpbb.import.forums.metadata'] = $app->share(
    function () use ($app) {
        $ret = array();
        $ret['nbItemsToImport'] = PhpBbForum::count();
        // Because of "parent_id" ids mapping, we have to process forums in a single batch
        $ret['nbItemsPerBatch'] = $ret['nbItemsToImport'];
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app['phpbb.import.forums.trigger_batch'] = $app->protect(
    function () use ($app) {

        $phpBbForums =
            PhpBbForum::query()
                ->orderBy('parent_id')
                ->get(array('forum_id', 'parent_id', 'forum_name', 'forum_desc', 'forum_posts', 'forum_topics'));

        // This array will allow us to map phpBb forums ids to our new Talk-Talk forums ids
        $idsMapping = array();

        $nbForumsCreated = 0;

        foreach ($phpBbForums as $phpBbForum) {

            $talkTalkForum = new TalkTalkForum();
            if (0 === $phpBbForum->parent_id) {
                $talkTalkForum->parent_id = null;
            } else {
                $talkTalkParentId = $idsMapping[$phpBbForum->parent_id];
                $talkTalkForum->parent_id = $talkTalkParentId;
            }
            $talkTalkForum->name = $phpBbForum->forum_name;
            $talkTalkForum->desc = $phpBbForum->forum_desc;
            $talkTalkForum->nb_posts = $phpBbForum->forum_posts;
            $talkTalkForum->nb_topics = $phpBbForum->forum_topics;

            try {
                $talkTalkForum->save();
            } catch (\PDOException $e) {
                if ('23000' === $e->getCode()) {
                    // We tolerate "Duplicate entry" Exceptions, but not others PDOExceptions
                    throw $e;
                }
            }

            $idsMapping[$phpBbForum->forum_id] = $talkTalkForum->id;

            $nbForumsCreated++;

        }

        return $nbForumsCreated;
    }
);
