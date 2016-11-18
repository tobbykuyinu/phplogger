<?php
use Tobby\PhpLogger\Logger;
use Psr\Log\LogLevel;

class LoggerTest extends PHPUnit_Framework_TestCase
{
    protected $outPath = 'tests/logs/out.log';
    protected $outLogDir = 'tests/logs';
    protected $defaultFile = 'logs.log';
    private $config;
    const LOG_CLASS = 'Tobby\PhpLogger\Logger';

    protected function setUp()
    {
        $this->config = [];
        @mkdir($this->outLogDir);
        @unlink($this->outPath);
        @unlink($this->defaultFile);
    }

    protected function tearDown()
    {
        @unlink($this->outPath);
        @unlink($this->defaultFile);
        @rmdir($this->outLogDir);
    }

    public function testCreatesSpecifiedFileIfNotExists()
    {
        $this->assertFalse(file_exists($this->outPath));
        $this->assertFalse(file_exists($this->defaultFile));

        $this->config['file'] = $this->outPath;
        $logger = new Logger($this->config);
        $logger->info('Info log message');

        $this->assertTrue(file_exists($this->outPath));
        $this->assertFalse(file_exists($this->defaultFile));
    }

    public function testCreatesDefaultFileIfNoneSpecified()
    {
        $this->assertFalse(file_exists($this->outPath));
        $this->assertFalse(file_exists($this->defaultFile));

        $logger = new Logger($this->config);
        $logger->info('Info log message');

        $this->assertFalse(file_exists($this->outPath));
        $this->assertTrue(file_exists($this->defaultFile));
    }

    public function testLogsToConsoleByDefault()
    {
        $logger = $this->getMockBuilder(self::LOG_CLASS)
            ->setConstructorArgs([$this->config])
            ->setMethods(['printToConsole'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $logger->expects($this->once())->method('printToConsole');
        $logger->info('message should log to console by default');
    }

    public function testLogsToConsoleIfSpecified()
    {
        $this->config['console'] = true;
        $logger = $this->getMockBuilder(self::LOG_CLASS)
        ->setConstructorArgs([$this->config])
        ->setMethods(['printToConsole'])
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $logger->expects($this->once())->method('printToConsole');
        $logger->info('Message should log to console when console = true');
    }

    public function testDoesNotLogToConsoleWhenSpecified()
    {
        $this->config['console'] = false;
        $logger = $this->getMockBuilder(self::LOG_CLASS)
            ->setConstructorArgs([$this->config])
            ->setMethods(['printToConsole'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $logger->expects($this->never())->method('printToConsole');
        $logger->info('Message should not log to console');
    }

    public function testDoesNotLogAboveSpecifiedLogLevel()
    {
        $this->config['level'] = LogLevel::INFO;
        $logger = $this->getMockBuilder(self::LOG_CLASS)
        ->setConstructorArgs([$this->config])
        ->setMethods(['makeMessage', 'write'])
        ->enableProxyingToOriginalMethods()
        ->getMock();

        $logger->expects($this->never())->method('makeMessage');
        $logger->expects($this->never())->method('write');
        $logger->debug('message');
    }

    public function testDefaultsToInfoLogLevel()
    {
        $logger = $this->getMockBuilder(self::LOG_CLASS)
            ->setConstructorArgs([$this->config])
            ->setMethods(['makeMessage', 'write'])
            ->enableProxyingToOriginalMethods()
            ->getMock();

        $logger->expects($this->never())->method('makeMessage');
        $logger->expects($this->never())->method('write');
        $logger->debug('message');
    }
}

