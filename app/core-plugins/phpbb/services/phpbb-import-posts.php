<?php

use TalkTalk\Model\Post as TalkTalkPost;
use TalkTalk\CorePlugins\PhpBb\Model\Post as PhpBbPost;

$app['phpbb.import.posts.nb_items_per_batch'] = 200;

$app['phpbb.import.posts.metadata'] = $app->share(
    function () use ($app) {
        $ret = array();
        $ret['nbItemsPerBatch'] = $app['phpbb.import.posts.nb_items_per_batch'];
        $ret['nbItemsToImport'] = PhpBbPost::query()->count();
        $ret['nbBatchesRequired'] = ceil($ret['nbItemsToImport'] / $ret['nbItemsPerBatch']);

        return $ret;
    }
);

$app['phpbb.import.posts.trigger_batch'] = $app->protect(
    function ($nbToImport, $from) use ($app) {

        $phpBbPosts =
            PhpBbPost::query()
                ->orderBy('post_id')
                ->skip($from)->take($nbToImport)
                ->get(array(
                    'post_id', 'forum_id', 'topic_id', 'poster_id', 'post_subject', 'post_text',
                    'bbcode_uid', 'post_time', 'post_edit_time'
                ));

        // This array allow us to map phpBb forums ids to our new Talk-Talk forums ids...
        $usersIdsMapping = $app['session']->get('phpbb.import.users.ids_mapping');
        if (null === $usersIdsMapping) {
            throw new \RuntimeException('No Users ids mapping found in Session. We need them for Posts import!');
        }

        // ...that one allow us to map phpBb forums ids to our new Talk-Talk forums ids...
        $forumsIdsMapping = $app['session']->get('phpbb.import.forums.ids_mapping');
        if (null === $forumsIdsMapping) {
            throw new \RuntimeException('No Forums ids mapping found in Session. We need them for Posts import!');
        }

        // ...and that one allow us to map phpBb topics ids to our new Talk-Talk topics ids
        $topicsIdsMapping = $app['session']->get('phpbb.import.topics.ids_mapping');
        if (null === $topicsIdsMapping) {
            throw new \RuntimeException('No Topics ids mapping found in Session. We need them for Posts import!');
        }

        $nbPostsCreated = 0;

        foreach ($phpBbPosts as $phpBbPost) {

            $talkTalkForumId = (isset($forumsIdsMapping[$phpBbPost->forum_id]))
                ? $forumsIdsMapping[$phpBbPost->forum_id]
                : null;
            $talkTalkTopicId = (isset($topicsIdsMapping[$phpBbPost->topic_id]))
                ? $topicsIdsMapping[$phpBbPost->topic_id]
                : null;
            $talkTalkAuthorId = (isset($usersIdsMapping[$phpBbPost->poster_id]))
                ? $usersIdsMapping[$phpBbPost->poster_id]
                : null;

            if (null === $talkTalkForumId || null === $talkTalkTopicId || null === $talkTalkAuthorId) {
                $nbPostsCreated++;
                continue;
            }

            $talkTalkPost = new TalkTalkPost();
            $talkTalkPost->forum_id = $talkTalkForumId;
            $talkTalkPost->topic_id = $talkTalkTopicId;
            $talkTalkPost->author_id = $talkTalkAuthorId;
            $talkTalkPost->title = html_entity_decode($phpBbPost->post_subject);
            $talkTalkPost->content = html_entity_decode($phpBbPost->post_text);
            // Let's get rid of the "bbcode_uid" of phpBb content:
            $talkTalkPost->content = str_replace(
                ':'.$phpBbPost->bbcode_uid,
                '',
                $talkTalkPost->content
            );
            $talkTalkPost->setCreatedAt($phpBbPost->post_time);
            $talkTalkPost->setUpdatedAt(
                0 === $phpBbPost->post_edit_time
                    ? $phpBbPost->post_time
                    : $phpBbPost->post_edit_time
            );
            $app['phpbb.import.add_provider_data']($talkTalkPost);
            $talkTalkPost->save();

            $nbPostsCreated++;

        }

        return $nbPostsCreated;
    }
);
