A simple config loader for PHP
==============================

[![Packagist](https://img.shields.io/packagist/v/buuum/config)](https://packagist.org/packages/buuum/config)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg?maxAge=2592000)](#license)

## Install

### System Requirements

You need PHP >= 5.5.0 to use Buuum\Config but the latest stable version of PHP is recommended.

### Composer

Buuum\Config is available on Packagist and can be installed using Composer:

```
composer require buuum/config
```

### Manually

You may use your own autoloader as long as it follows PSR-0 or PSR-4 standards. Just put src directory contents in your vendor directory.

##  INITIALIZE

```php
$configs = [
   'environment'  => 'local',

   'local' => [
      'host'        => 'host.dev',
      'public'      => 'httpdocs',
      'development' => true,
      'bbdd'        => [
          'database' => 'database_name',
          'host'     => '127.0.0.1',
          'username' => 'username',
          'password' => 'password'
      ]
  ],
  'prod' => [
         'host'        => 'host.com',
         'public'      => 'httpdocs',
         'development' => false,
         'bbdd'        => [
             'database' => 'database_name',
             'host'     => '127.0.0.1',
             'username' => 'username',
             'password' => 'password'
         ]
     ]

];

$autoloads = [
    "files" => ['functions.php'],
    "psr-4" => [
        "App\\Demo\\" => __DIR__."/src/Demo",
    ]
];

$config = new Config($configs, $autoloads);
```

## Get config values with dot notation
```php
$config->get('environment');
// return local
$config->get('local.host');
// return host.dev
$config->get('local.bbddd');
// return array
$config->get('local.bbdd.database);
// return database_name
```

##  parse errors
You need handleErrorInterface for parse Errors
Ex:
```php
class HandleError implements HandleErrorInterface
{

    private $logPath;
    private $debugMode;

    public function __construct($debugmode, $logPath = null)
    {
        $this->logPath = $logPath;
        $this->debugMode = $debugmode;
    }

    public function getDebugMode()
    {
        return $this->debugMode;
    }

    public function parseError($errtype, $errno, $errmsg, $filename, $linenum)
    {

        $err = "<errorentry>\n";
        $err .= "\t<datetime>" . date("Y-m-d H:i:s (T)") . "</datetime>\n";
        $err .= "\t<errornum>" . $errno . "</errornum>\n";
        $err .= "\t<errortype>" . $errtype . "</errortype>\n";
        $err .= "\t<errormsg>" . $errmsg . "</errormsg>\n";
        $err .= "\t<scriptname>" . $filename . "</scriptname>\n";
        $err .= "\t<scriptlinenum>" . $linenum . "</scriptlinenum>\n";

        $err .= "</errorentry>\n\n";

        error_log($err, 3, $this->logPath . "/error.log");

    }
}

$config->setupErrors($handle);
```
if debugMode is true show errors 
if debugMode is false parseErro is called



## LICENSE

The MIT License (MIT)

Copyright (c) 2016

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.