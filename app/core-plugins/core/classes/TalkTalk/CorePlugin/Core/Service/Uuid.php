<?php

namespace TalkTalk\CorePlugin\Core\Service;

class Uuid
{

    public function numeric()
    {
        return uniqid();
    }

}
