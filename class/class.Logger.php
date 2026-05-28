<?php

class Logger
{

    private $_details;

    public function __construct($details)
    {
        $this->_details = $details;
    }

    public function handler($type, $message)
    {
        @file_put_contents(Config::$LOG_DIR . date('Ymd') . '.log', date('Y-m-d H:i:s') . '|' . $type . '|' . $this->_details . ': ' . $message . PHP_EOL, FILE_APPEND);
    }
}
