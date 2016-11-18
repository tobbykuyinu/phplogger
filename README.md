# PHPLogger

A configurable Logger for PHP applications.

## Setup Instructions

* add the relevant repository in your composer.json like so:
    ```
     "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/tobbykuyinu/phplogger"
        }
     ]
    ```
* require the logger as a dependency using the command:
    
    `composer require tobbykuyinu/phplogger`
    
    Alternatively, you can include the package as a dependency in the composer.json file like so:
    
    ``` 
    "require": {
         "tobbykuyinu/phplogger": "^1.0"
     }
     ```
     
     Then run `composer install` afterwards
     
## Usage

* Assuming the (above) setup has been completed, here's a sample implementation of the logger:

    ```
    use \Tobby\PhpLogger\Logger;
    
    $logger = new Logger(['file' => 'logs/log.log', 'level' => 'debug', 'console' => false]);
    $logger->info("Info message");
    $logger->debug('Debug message');
    $logger->notice('Notice message');
    $logger->warning('Warning message');
    $logger->critical('Critical message');
    $logger->alert('Alert message');
    $logger->error('Error message', ['a' => 'context data', 'b' => 'more context data']);
    $logger->emergency('Emergency message');
    $logger->log('info', 'Info message');
    ```
    
    Only the `file` configuration array field is required. `level` defaults to `info` and `console` defaults to `true`
    Set `console` to false to prevent logging on the terminal (for example during script executions)