<?php

#______           _    _____           _                  
#|  _  \         | |  /  ___|         | |                 
#| | | |__ _ _ __| | _\ `--. _   _ ___| |_ ___ _ __ ___   
#| | | / _` | '__| |/ /`--. \ | | / __| __/ _ \ '_ ` _ \  
#| |/ / (_| | |  |   </\__/ / |_| \__ \ ||  __/ | | | | | 
#|___/ \__,_|_|  |_|\_\____/ \__, |___/\__\___|_| |_| |_| 
#                             __/ |                       
#                            |___/

interface Logger{

    /**
     * System is unusable
     *
     * @param string $message
     */
    public function emergency($message);

    /**
     * Action must me taken immediately
     *
     * @param string $message
     */
    public function alert($message);

    /**
     * Critical conditions
     *
     * @param string $message
     */
    public function critical($message);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     */
    public function error($message);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     */
    public function warning($message);

    /**
     * Normal but significant events.
     *
     * @param string $message
     */
    public function notice($message);

    /**
     * Inersting events.
     *
     * @param string $message
     */
    public function info($message);

    /**
     * Detailed debug information.
     *
     * @param string $message
     */
    public function debug($message);

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     */
    public function log($level, $message);

    /**
     * Logs a Throwable object
     *
     * @param Throwable $e
     * @param $trace
     */
    public function logException(\Throwable $e);
    
}
