<?php
namespace Buuum;

class Config
{
    /**
     * @var array
     */
    private $configs = [];

    /**
     * @var mixed
     */
    private $autoloads;

    /**
     * @var mixed
     */
    private $callback = false;

    /**
     * Config constructor.
     * @param array $configs
     * @param bool $autoloads
     */
    public function __construct(array $configs, $autoloads = false)
    {
        $this->configs = $configs;
        if ($autoloads) {
            $this->autoloads = $autoloads;
            $this->setAutoloads();
        }
    }

    /**
     * @param $name
     * @return array|bool|mixed
     */
    public function get($name)
    {
        if (isset($this->configs[$name])) {
            return $this->configs[$name];
        } elseif (strpos($name, '.') !== false) {
            $loc = &$this->configs;
            foreach (explode('.', $name) as $part) {
                $loc = &$loc[$part];
            }
            return $loc;
        }
        return false;
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        $this->configs[$name] = $value;
    }

    /**
     * @param callable $callback
     * @param bool $dev
     */
    public function setupErrors(callable $callback, $dev = true)
    {
        $this->callback = $callback;
        if (!$dev) {
            set_error_handler(array($this, "handleErrors"));
            register_shutdown_function(array($this, "shutdownFunction"));
        }

        $display_errors = $dev ? "1" : "0";
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', $display_errors);
        ini_set('html_errors', $display_errors);
    }

    /**
     * @param $errno
     * @param $errmsg
     * @param $filename
     * @param $linenum
     * @return bool
     */
    public function handleErrors($errno, $errmsg, $filename, $linenum)
    {
        if (0 == error_reporting()) {
            return true;
        }

        $errortype = array(
            E_ERROR             => 'Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Parsing Error',
            E_NOTICE            => 'Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error'
        );

        $errtype = (isset($errortype[$errno])) ? $errortype[$errno] : 'Unknow';

        $fn = $this->callback;
        $fn($errtype, $errno, $errmsg, $filename, $linenum);


        return true;
    }

    /**
     *
     */
    public function shutdownFunction()
    {
        $error = error_get_last();

        $save_errors = array(
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR
        );
        if ($error && in_array($error['type'], $save_errors)) {
            $errortypes = array(
                E_ERROR         => 'Fatal error',
                E_CORE_ERROR    => 'Fatal error (Core Error)',
                E_COMPILE_ERROR => 'Fatal error (Compile Error)'
            );

            $fn = $this->callback;
            $fn($errortypes[$error['type']], $error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     *
     */
    private function setAutoloads()
    {
        if (!empty($this->autoloads['files'])) {
            foreach ($this->autoloads['files'] as $file) {
                require_once $file;
            }
        }
        if (!empty($this->autoloads['psr-4'])) {
            spl_autoload_register(array($this, 'load'), true, true);
        }
    }

    /**
     * @param $classname
     */
    private function load($classname)
    {

        foreach ($this->autoloads['psr-4'] as $key => $dir) {
            if (substr($classname, 0, strlen($key)) == $key) {
                $name = substr($classname, strlen($key));
                require_once $dir . '/' . str_replace('\\', '/', $name) . ".php";
                return;
            }
        }


    }
}
