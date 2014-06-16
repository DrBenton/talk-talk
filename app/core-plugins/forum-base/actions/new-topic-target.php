<?php

use Symfony\Component\HttpFoundation\Request;
use TalkTalk\Model\Forum;
use TalkTalk\Model\Post;
use TalkTalk\Model\Topic;

$action = function (Request $request, Forum $forum) use ($app) {

    //TODO: validation

    $newTopicData = $request->request->get('topic');
    $newTopicData['content'] = $app->exec('forum-base.markup-manager.handle_forum_markup_before_save.smileys', $newTopicData['content']);

    $newTopic = new Topic($newTopicData);
    $newTopic->forum_id = $forum->id;
    $newTopic->provider = 'talk-talk';
    $newTopic->author_id = $app->get('user')->getUser()->id;
    $newTopic->save();

    $newPost = new Post($newTopicData);
    $newPost->forum_id = $forum->id;
    $newPost->topic_id = $newTopic->id;
    $newPost->author_id = $app->get('user')->getUser()->id;
    $newPost->provider = 'talk-talk';
    $newPost->save();

    $app->get('flash')->flashTranslated(
        'alerts.success.new-topic',
        'core-plugins.forum-base.new-topic.alerts.new-topic-successful',
        array()
    );

    // And now, we just have to display this new Topic!
    $targetUrl = $app->path('forum-base/topic', array(
        'topic' => $newTopic->id,
    ));
    if ($app->vars['isAjax']) {
        // JS response
        return $app->get('view')->render(
            'utils::common/simple-redirect.ajax',
            array('targetUrl' => $targetUrl)
        );
    } else {
        // Real HTTP redirection to the page
        return $app->redirect($targetUrl);
    }
};

return $action;
