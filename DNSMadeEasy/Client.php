<?php
namespace DNSMadeEasy;
use DNSMadeEasy\driver\Configuration;
use DNSMadeEasy\driver\REST;
use DNSMadeEasy\resource\Domains;
use DNSMadeEasy\resource\Records;
use DNSMadeEasy\resource\SoaRecords;
use DNSMadeEasy\resource\VanityDNS;
use DNSMadeEasy\resource\Templates;
use DNSMadeEasy\resource\TemplateRecords;
use DNSMadeEasy\resource\TransferACL;
use DNSMadeEasy\resource\Folders;
use DNSMadeEasy\resource\Usage;
use DNSMadeEasy\resource\Failover;
use DNSMadeEasy\resource\Secondary;
use DNSMadeEasy\resource\SecondaryRecords;

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
     * Domain management commands.
     * @var Domains
     */
    public $domains;

    /**
     * Record mangement commands.
     * @var Records
     */
    public $records;

    /**
     * SOA record management commands.
     * @var SoaRecords
     */
    public $soaRecords;

    /**
     * VanityDNS management commands.
     * @var VanityDNS
     */
    public $vanityDNS;

    /**
     * Template management commands.
     * @var Templates
     */
    public $templates;

    /**
     * Template record management commands.
     * @var TemplateRecords
     */
    public $templateRecords;

    /**
     * TransferACL management commands.
     * @var TransferACL
     */
    public $transferACL;

    /**
     * Folder management commands.
     * @var Folder
     */
    public $folder;

    /**
     * Usage commands.
     * @var Usage
     */
    public $usage;

    /**
     * Failover management commands.
     * @var Failover
     */
    public $failover;

    /**
     * Secondary management commands.
     * @var Secondary
     */
    public $secondary;

    /**
     * Secondary records managementcommands.
     * @var SecondaryRecords
     */
    public $secondaryRecords;

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

        //Setup the commands.
        $this->domains = new Domains($this->_driver);
        $this->records = new Records($this->_driver);
        $this->soaRecords = new SoaRecords($this->_driver);
        $this->vanityDNS = new VanityDNS($this->_driver);
        $this->templates = new Templates($this->_driver);
        $this->templateRecords = new TemplateRecords($this->_driver);
        $this->transferACL = new TransferACL($this->_driver);
        $this->folders = new Folders($this->_driver);
        $this->usage = new Usage($this->_driver);
        $this->failover = new Failover($this->_driver);
        $this->secondary = new Secondary($this->_driver);
        $this->secondaryRecords = new SecondaryRecords($this->_driver);
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
