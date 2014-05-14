<?php

use TalkTalk\Model\Forum;

//TODO: use data cache for this tree
$app['forum.forums-data.tree'] = $app->share(
    function () use ($app) {

        $forumsFlat = Forum::query()
            ->orderBy('parent_id')
            ->orderBy('id')
            ->get();

        $forumsTree = array();
        foreach ($forumsFlat as $forum) {

            if (null === $forum->parent_id) {
                // This forum has no parent: we just have to add it to the root forums array
                $forumsTree[] = $forum;
            } else {
                // We have to find this forum parent!
                $parentForum = Forum::getInstance($forum->parent_id);
                if (null === $parentForum) {
                    throw new \RuntimeException(
                        sprintf('No parent forum with id "%u" found for forum "%u" ("%s")!', $forum->parent_id, $forum->id, $forum->name)
                    );
                }
                $parentForum->addChild($forum);
                $forum->setParent($parentForum);
            }

        }

        return $forumsTree;
    }
);