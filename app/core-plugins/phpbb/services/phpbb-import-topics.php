<?php

use TalkTalk\Model\Topic as TalkTalkTopic;
use TalkTalk\CorePlugins\PhpBb\Model\Topic as PhpBbTopic;

$app['phpbb.import.topics.nb_items_per_batch'] = 100;

$app['phpbb.import.topics.metadata'] = $app->share(
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app['phpbb.import.topics.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbTopic::query()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app['phpbb.import.topics.trigger_batch'] = $app->protect(
    function ($nbToImport, $from) use ($app) {

        $phpBbTopics =
            PhpBbTopic::query()
                ->orderBy('topic_id')
                ->skip($from)->take($nbToImport)
                ->get(array(
                    'topic_id', 'forum_id', 'topic_title', 'topic_poster', 'topic_replies',
                    'topic_time', 'topic_last_post_time'
                ));

        // This array will allow us to map phpBb topics ids to our new Talk-Talk topics ids
        // This may be useful for next imports processes
        $idsMapping = $app['session']->get('phpbb.import.topics.ids_mapping', array());

        // ...this one allow us to map phpBb forums ids to our new Talk-Talk forums ids...
        $usersIdsMapping = $app['session']->get('phpbb.import.users.ids_mapping');
        if (null === $usersIdsMapping) {
            throw new \RuntimeException('No Users ids mapping found in Session. We need them for Topics import!');
        }

        // ...and that one allow us to map phpBb forums ids to our new Talk-Talk forums ids
        $forumsIdsMapping = $app['session']->get('phpbb.import.forums.ids_mapping');
        if (null === $forumsIdsMapping) {
            throw new \RuntimeException('No Forums ids mapping found in Session. We need them for Topics import!');
        }

        $nbTopicsCreated = 0;

        foreach ($phpBbTopics as $phpBbTopic) {

            $talkTalkForumId = (isset($forumsIdsMapping[$phpBbTopic->forum_id]))
                ? $forumsIdsMapping[$phpBbTopic->forum_id]
                : null;
            $talkTalkAuthorId = (isset($usersIdsMapping[$phpBbTopic->topic_poster]))
                ? $usersIdsMapping[$phpBbTopic->topic_poster]
                : null;

            $talkTalkTopic = new TalkTalkTopic();
            $talkTalkTopic->forum_id = $talkTalkForumId;
            $talkTalkTopic->author_id = $talkTalkAuthorId;
            $talkTalkTopic->name = $phpBbTopic->topic_title;
            $talkTalkTopic->nb_replies = $phpBbTopic->topic_replies;
            $talkTalkTopic->setCreatedAt($phpBbTopic->topic_time);
            $talkTalkTopic->setUpdatedAt($phpBbTopic->topic_last_post_time);
            $talkTalkTopic->save();

            $idsMapping[$phpBbTopic->topic_id] = $talkTalkTopic->id;

            $nbTopicsCreated++;

        }

        // We have to keep the topics ids in our Session data, as other phpBb import processes may need it
        $app['session']->set('phpbb.import.topics.ids_mapping', $idsMapping);

        return $nbTopicsCreated;
    }
);
