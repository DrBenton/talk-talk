<?php

use TalkTalk\Model\Topic;

$converter = function ($topicId) {
    return Topic::findOrFail((int) $topicId);
};

return $converter;