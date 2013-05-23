<?php
namespace tests;
use DNSMadeEasy\Client;

/**
 * Base class for the DNSMadeEasy test suite.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Base extends \PHPUnit_Framework_TestCase
{
    /**
     * The api key.
     * @var string
     */
    private $_apiKey;

    /**
     * The secret key.
     * @var string
     */
    private $_secretKey;

    /**
     * Grab the api key and secret key from the environment.
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
    	$this->_apiKey = getenv('APIKEY');
        $this->_secretKey = getenv('SECRETKEY');
        
    	parent::__construct($name, $data, $dataName);
    }
    
    /**
     * Get an instance of the DNSMadeEasy client.
     * @param  string              $apiKey     The api key.
     * @param  string              $secretKey  The secret key.
     * @param  string              $useSandbox Whether to use the sandbox or not.
     * @return \DNSMadeEasy\Client
     */
    protected function getClient($apiKey = null, $secretKey = null, $useSandbox = true)
    {
        if (!$apiKey) {
            $apikey = $this->getApiKey();
        }

        if (!$secretKey) {
            $secretKey = $this->getSecretKey();
        }

        return $client = new Client($apikey, $secretKey, $useSandbox);
    }

    /**
     * Get the api key.
     * @return string
     */
    protected function getApiKey()
    {
        return $this->_apiKey;
    }

    /**
     * Get the secret key.
     * @return string
     */
    protected function getSecretKey()
    {
        return $this->_secretKey;
    }
}
