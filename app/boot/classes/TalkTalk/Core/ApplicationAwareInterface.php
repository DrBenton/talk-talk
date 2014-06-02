<?php

namespace TalkTalk\Core;

use TalkTalk\Core\Application;

interface ApplicationAwareInterface
{
    public function setApplication(Application $app);
}