<?php
namespace Tobby\PhpLogger;

use Error;
use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger {

    private $file;
    private $logLevel;
    private $console;
    public $lastLogEntry;

    protected $logLevels = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    /**
     * Logger constructor.
     * @param array $config - configuration array with the following possible keys:
     *      - file (required) - the path to the log file
     *      - level (optional) - the log level. Defaults to 'info'
     *      - console (optional) - option for logging to console. Defaults to true
     * @throws Error
     */
    public function __construct(array $config)
    {
        if (!isset($config['file']) || empty($config['file'])) {
            throw new Error('Please set a log path');
        }

        $file = $config['file'];
        $fh = fopen($file, 'a');

        if (!$fh) {
            throw new Error("Unable to open log file: {$file}");
        }

        $this->file = $fh;

        $level = (isset($config['level']) && in_array(strtolower($config['level']), array_keys($this->logLevels))) ?
            strtolower($config['level']) : LogLevel::INFO;
        $this->logLevel = $level;

        $this->console = isset($config['console']) ? boolval($config['console']) : true;
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
    {
        if ($this->logLevels[$this->logLevel] >= $this->logLevels[$level]) {
            $message = $this->makeMessage($level, $message, $context);
            $this->write($message);
        }
    }

    /**
     * @param string $message
     * @throws Error
     */
    protected function write($message)
    {
        $message .= "\n";

        if ($this->console) {
            $this->printToConsole($message);
        }

        if (fwrite($this->file, $message) === false) {
            throw new Error('Failed to write to log file');
        }

        $this->lastLogEntry = trim($message);
    }

    protected function printToConsole($message)
    {
        print($message);
    }

    /**
     * @param string $level
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function makeMessage($level, $message, $context)
    {
        $context = empty($context) ? '' : json_encode($context);
        $message = $message . ' ' . $context;
        return sprintf('[%s][%s]  %s', date('Y-m-d H:i:s', microtime(true)), strtoupper($level), $message);
    }

    /**
     *  Class destructor
     */
    public function __destruct()
    {
        @fclose($this->file);
    }
}
