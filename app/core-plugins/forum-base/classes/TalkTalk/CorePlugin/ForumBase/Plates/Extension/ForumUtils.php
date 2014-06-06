<?php

namespace TalkTalk\CorePlugin\ForumBase\Plates\Extension;

use TalkTalk\CorePlugin\Core\Plates\Extension\BaseExtension;

class ForumUtils extends BaseExtension
{

    public function getFunctions()
    {
        return array(
            'forumUtils' => 'getForumUtilsObject'
        );
    }

    public function getForumUtilsObject()
    {
        return $this;
    }

    public function bbcodeToHtml($bbcode)
    {
        return $this->app->exec('forum-base.markup-manager.handle_forum_markup.all', $bbcode);
    }

}
