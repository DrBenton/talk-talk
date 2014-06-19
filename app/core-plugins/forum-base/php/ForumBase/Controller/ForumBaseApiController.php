<?php

namespace TalkTalk\CorePlugin\ForumBase\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use TalkTalk\CorePlugin\Core\Controller\BaseController;
use TalkTalk\CorePlugin\ForumBase\Model\Forum;

class ForumBaseApiController extends BaseController
{

    public function forums()
    {
        $allForums = Forum::getTree();
        $allForums = array_map(
            function (Forum $forum) {
                return $forum->toArray();
            },
            $allForums
        );
        return new JsonResponse($allForums);
    }


}