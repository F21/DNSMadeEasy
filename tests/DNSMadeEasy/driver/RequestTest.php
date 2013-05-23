<?php
namespace tests\DNSMadeEasy\driver;
use tests\Base;
use DNSMadeEasy\driver\Request;

/**
 * Tests for the driver request object.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class RequestTest extends Base
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * The curl information for testing.
     * @var array
     */
    protected $curlInfo = array('url' => 'http://api.sandbox.dnsmadeeasy.com/V2.0/dns/managed',
                            'content_type' => 'application/json',
                            'http_code' => 200,
                            'header_size' => 339,
                            'request_size' => 244,
                            'filetime' => -1,
                            'ssl_verify_result' => 1,
                            'redirect_count' => 0,
                            'total_time' => 1.230122,
                            'namelookup_time' => 0.629414,
                            'connect_time' => 0.904966,
                            'pretransfer_time' => 0.905243,
                            'size_upload' => 0,
                            'size_download' => 709,
                            'speed_download' => 576,
                            'speed_upload' => 0,
                            'download_content_length' => -1,
                            'upload_content_length' => 0,
                            'starttransfer_time' => 1.228725,
                            'redirect_time' => 0,
                            'certinfo' => array(),
                            'primary_ip' => '208.94.147.116',
                            'primary_port' => 80,
                            'local_ip' => '192.168.1.20',
                            'local_port' => 49636,
                            'redirect_url' => '',
                            'request_header' => "POST /V2.0/dns/managed HTTP/1.1\r\nHost: api.sandbox.dnsmadeeasy.com\r\nAccept: */*\r\nx-dnsme-apiKey: 123-456-789\r\nx-dnsme-requestDate: Wed, 22 May 2013 06:20:31 UTC\r\nx-dnsme-hmac: 7d4fc7bdsdfa3agd429f280b0289dbc7058cf7c4e"
                    );

    /**
     * The request body.
     * @var array
     */
    protected $body = array('names' => array('test.com'));

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->body = json_encode($this->body);
        $this->request = new Request($this->curlInfo, $this->body);
    }

    /**
     * @covers DNSMadeEasy\driver\Request::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Request');

        $rawHeaders = $reflectionClass->getProperty('_rawHeaders');
        $rawHeaders->setAccessible(true);

        $body = $reflectionClass->getProperty('_body');
        $body->setAccessible(true);

        $method = $reflectionClass->getProperty('_method');
        $method->setAccessible(true);

        $version = $reflectionClass->getProperty('_version');
        $version->setAccessible(true);

        $url = $reflectionClass->getProperty('_url');
        $url->setAccessible(true);

        $headers = $reflectionClass->getProperty('_headers');
        $headers->setAccessible(true);

        $request = new Request($this->curlInfo, $this->body);

        $this->assertEquals($this->curlInfo['request_header'], $rawHeaders->getValue($request), 'The request headers do not match');
        $this->assertEquals($this->body, $body->getValue($request), 'The body does not match');
        $this->assertEquals('POST', $method->getValue($request), 'The method does not match');
        $this->assertEquals('1.1', $version->getValue($request), 'The version does not match');
        $this->assertEquals('/V2.0/dns/managed', $url->getValue($request), 'The url does not match');
        $this->assertInternalType('array', $headers->getValue($request), 'The headers should be an array');
        $this->assertNotEmpty($headers->getValue($request), 'The headers should not be empty');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getMethod
     */
    public function testGetMethod()
    {
        $this->assertEquals('POST', $this->request->getMethod(), 'The method does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals('1.1', $this->request->getVersion(), 'The version does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getUrl
     */
    public function testGetUrl()
    {
        $this->assertEquals('/V2.0/dns/managed', $this->request->getUrl(), 'The url does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getHeaders
     */
    public function testGetHeaders()
    {
        $this->assertInternalType('array', $this->request->getHeaders(), 'The headers should be an array');
        $this->assertNotEmpty($this->request->getHeaders(), 'The headers array should not be empty');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getBody
     */
    public function testGetBody()
    {
        $this->assertEquals($this->body, $this->request->getBody(), 'The body does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::getRawHeaders
     */
    public function testGetRawHeaders()
    {
        $this->assertEquals($this->curlInfo['request_header'], $this->request->getRawHeaders(), 'The raw headers do not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Request::parseHeaders
     */
    public function testParseHeaders()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Request');

        $parseHeaders = $reflectionClass->getMethod('parseHeaders');
        $parseHeaders->setAccessible(true);

        $parsed = $parseHeaders->invoke($this->request, $this->curlInfo['request_header']);

        $this->assertInternalType('array', $parsed, 'The parsed result should be an array');

        $this->assertArrayHasKey('method', $parsed, 'The parsed result should contain a method element');
        $this->assertEquals('POST', $parsed['method'], 'The parsed method does not match');

        $this->assertArrayHasKey('url', $parsed, 'The parsed result should contain a url element');
        $this->assertEquals('/V2.0/dns/managed', $parsed['url'], 'The parsed url does not match');

        $this->assertArrayHasKey('version', $parsed, 'The parsed result should contain a version element');
        $this->assertEquals('1.1', $parsed['version'], 'The parsed version does not match');

        $this->assertArrayHasKey('headers', $parsed, 'The parsed result should contain a headers element');
        $this->assertInternalType('array', $parsed['headers'], 'The parsed headers should be an array');
        $this->assertNotEmpty($parsed['headers'], 'The headers array should not be empty');
    }
}
