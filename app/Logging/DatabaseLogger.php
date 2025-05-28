<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Illuminate\Support\Str;

class DatabaseLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @return Logger
     */


    public function __invoke(array $config)
    {
        $handler = new DatabaseHandler();

        $logger =  new Logger($config['driver'], [$handler]);
        //Add level
        $logger->pushHandler(new StreamHandler(storage_path("logs/critical.log"), Logger::CRITICAL));
        $logger->pushHandler(new StreamHandler(storage_path("logs/error.log"), Logger::ERROR));
        $logger->pushHandler(new StreamHandler(storage_path("logs/info.log"), Logger::INFO));
        $logger->pushHandler(new StreamHandler(storage_path("logs/notice.log"), Logger::NOTICE));
        $logger->pushHandler(new StreamHandler(storage_path("logs/emergency.log"), Logger::EMERGENCY));

        return $logger;
    }
}
