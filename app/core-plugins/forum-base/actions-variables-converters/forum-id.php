<?php

use TalkTalk\Model\Forum;

$converter = function ($forumId) {
    return Forum::findOrFail((int) $forumId);
};

return $converter;
