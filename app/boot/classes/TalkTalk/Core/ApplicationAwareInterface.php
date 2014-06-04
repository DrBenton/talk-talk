<?php

namespace TalkTalk\Core;

interface ApplicationAwareInterface
{
    public function setApplication(ApplicationInterface $app);
}
