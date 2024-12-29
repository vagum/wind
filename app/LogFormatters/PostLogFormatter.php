<?php

namespace App\LogFormatters;

use Illuminate\Log\Logger;
use Monolog\Formatter\LineFormatter;

class PostLogFormatter
{
    public function __invoke(Logger $logger): void
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                '%message%'.PHP_EOL
            ));
        }
    }
}
