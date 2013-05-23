<?php
namespace tests\DNSMadeEasy;
use tests\Base;
use DNSMadeEasy\Client;

/**
 * Tests for the client.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class ClientTest extends Base
{
    /**
     * An instance of the client.
     * @var Client
     */
    protected $client;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->client = $this->getClient();
    }

    /**
     * @covers DNSMadeEasy\Client::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $reflectionClass->getProperty('_config');
        $configuration->setAccessible(true);

        $driver = $reflectionClass->getProperty('_driver');
        $driver->setAccessible(true);

        $client = new Client($this->getApiKey(), $this->getSecretKey(), true);

        $this->assertInstanceOf('DNSMadeEasy\driver\Configuration', $configuration->getValue($client), 'The configuration object should be of the type DNSMadeEasy\driver\Configuration');
        $this->assertInstanceOf('DNSMadeEasy\driver\REST', $driver->getValue($client), 'The REST driver should be of the type DNSMadeEasy\driver\REST');
        $this->assertInstanceOf('DNSMadeEasy\resource\Domains', $client->domains, 'The domains manager should be of the type DNSMadeEasy\resource\Domains');
        $this->assertInstanceOf('DNSMadeEasy\resource\Records', $client->records, 'The records manager should be of the type DNSMadeEasy\resource\Records');
        $this->assertInstanceOf('DNSMadeEasy\resource\SoaRecords', $client->soaRecords, 'The SoA records manager should be of the type DNSMadeEasy\resource\SoaRecords');
        $this->assertInstanceOf('DNSMadeEasy\resource\VanityDNS', $client->vanityDNS, 'The vanity DNS manager should be of the type DNSMadeEasy\resource\VanityDNS');
        $this->assertInstanceOf('DNSMadeEasy\resource\Templates', $client->templates, 'The templates manager should be of the type DNSMadeEasy\resource\Templates');
        $this->assertInstanceOf('DNSMadeEasy\resource\TemplateRecords', $client->templateRecords, 'The template recorsd manager should be of the type DNSMadeEasy\resource\TemplateRecords');
        $this->assertInstanceOf('DNSMadeEasy\resource\TransferACL', $client->transferACL, 'The transfer ACL manager should be of the type DNSMadeEasy\resource\TransferACL');
        $this->assertInstanceOf('DNSMadeEasy\resource\Folders', $client->folders, 'The folders manager should be of the type DNSMadeEasy\resource\Folders');
        $this->assertInstanceOf('DNSMadeEasy\resource\Usage', $client->usage, 'The usage manager should be of the type DNSMadeEasy\resource\Usage');
        $this->assertInstanceOf('DNSMadeEasy\resource\Failover', $client->failover, 'The failover manager should be of the type DNSMadeEasy\resource\Failover');
        $this->assertInstanceOf('DNSMadeEasy\resource\Secondary', $client->secondary, 'The secondary manager should be of the type DNSMadeEasy\resource\Secondary');
        $this->assertInstanceOf('DNSMadeEasy\resource\SecondaryRecords', $client->secondaryRecords, 'The secondary records manager should be of the type DNSMadeEasy\resource\SecondaryRecords');
    }

    /**
     * @covers DNSMadeEasy\Client::useSandbox
     */
    public function testUseSandbox()
    {
        $clientClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $clientClass->getProperty('_config');
        $configuration->setAccessible(true);

        $configurationClass = new \ReflectionClass('DNSMadeEasy\driver\Configuration');

        $useSandbox = $configurationClass->getProperty('_useSandbox');
        $useSandbox->setAccessible(true);

        $this->client->useSandbox(false);
        $this->assertFalse($useSandbox->getValue($configuration->getValue($this->client)), "useSandbox should be false");

        $this->client->useSandbox(true);
        $this->assertTrue($useSandbox->getValue($configuration->getValue($this->client)), "useSandbox should be true");
    }

    /**
     * @covers DNSMadeEasy\Client::debug
     */
    public function testDebug()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\Client');

        $configuration = $reflectionClass->getProperty('_config');
        $configuration->setAccessible(true);

        $this->client->debug(false);
        $this->assertFalse($configuration->getValue($this->client)->getDebug(), "Debug should be false");

        $this->client->debug(true);
        $this->assertTrue($configuration->getValue($this->client)->getDebug(), "Debug should be true");
    }
}
