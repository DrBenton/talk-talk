<?php

namespace TalkTalk\Kernel\Service;

use Teacup\Log;

class Logger extends Log /* implements Psr\Log\LoggerInterface */
{

    public function __construct($logsDir)
    {
        $outputFileName = date('Y-m-d') . '.log' ;
        parent::__construct($logsDir, $outputFileName);
    }

}
