<?php

namespace Blackbird\TicketBlaster\Logger\Handler;

use Monolog\Logger;

class Warning extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::WARNING;
    
    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/myCustomWarning.log';
}