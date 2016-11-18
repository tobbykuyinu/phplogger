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

    /**
     * Provision tests
     */
    protected function setUp()
    {
        $this->config = [];
        @mkdir($this->outLogDir);
        @unlink($this->outPath);
        @unlink($this->defaultFile);
    }

    /**
     * Unset test infrastructure
     */
    protected function tearDown()
    {
        @unlink($this->outPath);
        @unlink($this->defaultFile);
        @rmdir($this->outLogDir);
    }

    /**
     * @test Create specified file if not exists
     */
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

    /**
     * @test creates a default file if none is specified
     */
    public function testCreatesDefaultFileIfNoneSpecified()
    {
        $this->assertFalse(file_exists($this->outPath));
        $this->assertFalse(file_exists($this->defaultFile));

        $logger = new Logger($this->config);
        $logger->info('Info log message');

        $this->assertFalse(file_exists($this->outPath));
        $this->assertTrue(file_exists($this->defaultFile));
    }

    /**
     * @test logs to console by default
     */
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

    /**
     * @test log to console if specified
     */
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

    /**
     * @test does not log to console when specified
     */
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

    /**
     * @test does not log above specified log level
     */
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

    /**
     * @test defaults to info log level
     */
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

