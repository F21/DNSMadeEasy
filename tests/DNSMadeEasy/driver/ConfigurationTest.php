<?php
namespace tests\DNSMadeEasy\driver;
use tests\Base;
use DNSMadeEasy\driver\Configuration;

/**
 * Tests for the configuration object.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class ConfigurationTest extends Base
{
    /**
     * An instance of the configuration object.
     * @var Configuration
     */
    protected $configuration;

    /**
     * The api key.
     * @var string
     */
    protected $apiKey = '123456';

    /**
     * The api secret.
     * @var string
     */
    protected $secretKey = 'abcdefg';

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->configuration = new Configuration($this->apiKey, $this->secretKey);
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Configuration');

        $apiKey = $reflectionClass->getProperty('_apiKey');
        $apiKey->setAccessible(true);

        $secretKey = $reflectionClass->getProperty('_secretKey');
        $secretKey->setAccessible(true);

        $useSandbox = $reflectionClass->getProperty('_useSandbox');
        $useSandbox->setAccessible(true);

        $configuration = new Configuration($this->apiKey, $this->secretKey, true);

        $this->assertEquals($this->apiKey, $apiKey->getValue($configuration), 'The api key does not match');
        $this->assertEquals($this->secretKey, $secretKey->getValue($configuration), 'The secret key does not match');
        $this->assertTrue($useSandbox->getValue($configuration), "_useSandbox should be true");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::getAPIKey
     */
    public function testGetAPIKey()
    {
        $this->assertEquals($this->apiKey, $this->configuration->getAPIKey(), "The api keys do not match");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::getSecretKey
     */
    public function testGetSecretKey()
    {
        $this->assertEquals($this->secretKey, $this->configuration->getSecretKey(), "The secret keys do not match");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::getURL
     */
    public function testGetURL()
    {
        $configuration = new Configuration($this->apiKey, $this->secretKey, true);

        $this->assertEquals('https://api.sandbox.dnsmadeeasy.com/V2.0', $configuration->getURL(), "The sandbox url should be returned");

        $configuration = new Configuration($this->apiKey, $this->secretKey, false);

        $this->assertEquals('https://api.dnsmadeeasy.com/V2.0', $configuration->getURL(), "The sandbox url should be returned");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::useSandbox
     */
    public function testUseSandbox()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Configuration');

        $useSandbox = $reflectionClass->getProperty('_useSandbox');
        $useSandbox->setAccessible(true);

        $this->configuration->useSandbox(true);
        $this->assertTrue($useSandbox->getValue($this->configuration), "useSandbox should be true");

        $this->configuration->useSandbox(false);
        $this->assertFalse($useSandbox->getValue($this->configuration), "useSandbox should be false");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::debug
     */
    public function testDebug()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Configuration');

        $debug = $reflectionClass->getProperty('_debug');
        $debug->setAccessible(true);

        $this->configuration->debug(true);
        $this->assertTrue($debug->getValue($this->configuration), "debug should be true");

        $this->configuration->debug(false);
        $this->assertFalse($debug->getValue($this->configuration), "debug should be false");
    }

    /**
     * @covers DNSMadeEasy\driver\Configuration::getDebug
     */
    public function testGetDebug()
    {
        $this->configuration->debug(true);
        $this->assertTrue($this->configuration->getDebug(), "Debug should be set to true");

        $this->configuration->debug(false);
        $this->assertFalse($this->configuration->getDebug(), "Debug should be set to false");
    }
}
