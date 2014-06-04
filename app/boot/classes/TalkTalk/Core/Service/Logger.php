<?php

namespace TalkTalk\Core\Service;

use Slim\Log;
use Slim\LogWriter;

class Logger extends Log /* implements Psr\Log\LoggerInterface */
{

    public function __construct($logsDir)
    {
        $outputFile = $logsDir . '/' . date('Y-m-d') . '.log' ;
        $output = fopen($outputFile, 'a');
        parent::__construct(new LogWriter($output));
    }

    public function log($level, $object, $context = array())
    {
        $object = date('Y-m-d H:i:s') . ' - ' . (string) self::$levels[$level] . ' : ' . (string) $object ;

        return parent::log($level, $object, $context);
    }

}
