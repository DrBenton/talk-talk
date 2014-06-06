<?php

namespace TalkTalk\CorePlugin\Core\Service;

class Uuid
{

    public function numeric()
    {
        return round(microtime(true));
    }

    public function alnum()
    {
        return uniqid();
    }

}
