<?php

namespace TalkTalk\Core\Service;

use Teacup\Log;

class Logger extends Log /* implements Psr\Log\LoggerInterface */
{

    public function __construct($logsDir)
    {
        $outputFileName = date('Y-m-d') . '.log' ;
        parent::__construct($logsDir, $outputFileName);
    }

    /*
    public function log($level, $object, $context = array())
    {
        $object = date('Y-m-d H:i:s') . ' - ' . (string) self::$levels[$level] . ' : ' . (string) $object ;

        return parent::log($level, $object, $context);
    }
    */

}
