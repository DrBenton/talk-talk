<?php

namespace TalkTalk\Kernel;

interface ApplicationAwareInterface
{
    public function setApplication(ApplicationInterface $app);
}
