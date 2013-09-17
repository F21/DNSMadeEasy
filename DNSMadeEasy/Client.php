<?php
namespace DNSMadeEasy;
use DNSMadeEasy\driver\Configuration;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Client
 * The client provides a unified entry point to interact with DNSMadeEasy's API.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Client
{
    /**
     * Driver configuration for the client.
     * @var Configuration
     */
    private $_config;

    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Construct the client.
     * @param string  $apiKey     DNSMadeEasy API key.
     * @param string  $secretKey  DNSMadeEasy secret key.
     * @param boolean $useSandbox Whether to use the sandbox or not.
     */
    public function __construct($apiKey, $secretKey, $useSandbox = false)
    {
        //Set up the driver.
        $this->_config = new Configuration($apiKey, $secretKey, $useSandbox);
        $this->_driver = new REST($this->_config);
    }
    
    public function __call($method, $args)
    {
    	$class_name = '\DNSMadeEasy\resource\\' . ucfirst($method);
    
    	if (class_exists($class_name)) {
    		$class = new $class_name($this->_driver);
    
    		return $class;
    	} else {
    		throw new \BadMethodCallException('Call to undefined method '.get_class($this).'::'.$method.'()');
    	}
    }

    /**
     * Sets whether the client should use the sandbox or not.
     * @param boolean $value Whether to use the sandbox or not.
     */
    public function useSandbox($value)
    {
        $this->_config->useSandbox($value);
    }

    /**
     * Turns the debugger on and off.
     * @param boolean $value Whether to turn the debugger on or off.
     */
    public function debug($value)
    {
        $this->_config->debug($value);
    }
}
