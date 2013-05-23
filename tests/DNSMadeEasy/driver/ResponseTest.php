<?php
namespace tests\DNSMadeEasy\driver;
use tests\Base;
use DNSMadeEasy\driver\Response;

/**
 * Tests for the driver response object.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class ResponseTest extends Base
{
    /**
     * An instance of the response object.
     * @var Response
     */
    protected $response;

    /**
     * Sample response.
     * @var string
     */
    protected $responseData =
"HTTP/1.1 200 OK\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n
{\"data\":[{\"name\":\"test.com\",\"id\":861223,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"vanityId\":12241,\"created\":1369094400000,\"updated\":1369139723297,\"processMulti\":false},{\"name\":\"test1.com\",\"id\":861221,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"created\":1369094400000,\"updated\":1369139430632,\"processMulti\":false}],\"page\":0,\"totalPages\":1,\"totalRecords\":2}";

    /**
     * Sample time taken.
     * @var float
     */
    protected $timeTaken = 1.1;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->response = new Response($this->responseData, $this->timeTaken);
    }

    /**
     * @covers DNSMadeEasy\driver\Response::__construct
     */
    public function testConstructor()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $rawHeaders = $reflectionClass->getProperty('_rawHeaders');
        $rawHeaders->setAccessible(true);

        $version = $reflectionClass->getProperty('_version');
        $version->setAccessible(true);

        $statusCode = $reflectionClass->getProperty('_statusCode');
        $statusCode->setAccessible(true);

        $headers = $reflectionClass->getProperty('_headers');
        $headers->setAccessible(true);

        $body = $reflectionClass->getProperty('_body');
        $body->setAccessible(true);

        $timeTaken = $reflectionClass->getProperty('_timeTaken');
        $timeTaken->setAccessible(true);

        $response = new Response($this->responseData, $this->timeTaken);

        $this->assertInternalType('string', $rawHeaders->getValue($response), 'The raw headers should be a string');
        $this->assertEquals('1.1', $version->getValue($response), 'The version does not match');
        $this->assertEquals(200, $statusCode->getValue($response), "The status code should be 200");
        $this->assertInternalType('array', $headers->getValue($response), 'The headers should be an array');
        $this->assertNotEmpty($headers->getValue($response), 'The headers should not be empty');
        $this->assertInternalType('string', $body->getValue($response), 'The body should be a string');
        $this->assertEquals(1.1, $timeTaken->getValue($response), 'The time taken does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getVersion
     */
    public function testGetVersion()
    {
        $this->assertEquals('1.1', $this->response->getVersion(), 'The version does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getStatusCode
     */
    public function testGetStatusCode()
    {
        $this->assertEquals(200, $this->response->getStatusCode(), 'The status code does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getStatus
     */
    public function testGetStatus()
    {
        $this->assertEquals("OK", $this->response->getStatus(), 'The status does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getHeaders
     */
    public function testGetHeaders()
    {
        $this->assertInternalType('array', $this->response->getHeaders(), 'The headers should be an array');
        $this->assertNotEmpty($this->response->getHeaders(), 'The headers array should not be empty');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getBody
     */
    public function testGetBody()
    {
        $this->assertInternalType('string', $this->response->getBody(), "The body should be a string");
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getTimeTaken
     */
    public function testGetTimeTaken()
    {
        $this->assertEquals(1.1, $this->response->getTimeTaken(), 'The time taken does not match');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::getRawHeaders
     */
    public function testGetRawHeaders()
    {
        $this->assertInternalType('string', $this->response->getRawHeaders(), 'The raw headers should be a string');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::parseMessage
     */
    public function testParseMessage()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $parseMessage = $reflectionClass->getMethod('parseMessage');
        $parseMessage->setAccessible(true);

        $parsed = $parseMessage->invoke($this->response, $this->responseData);

        $this->assertInternalType('array', $parsed, 'The parsed result should be an array');

        $this->assertArrayHasKey('headers', $parsed, 'The parsed result should contain a headers key');
        $this->assertInternalType('string', $parsed['headers'], "The headers should be a string");

        $this->assertArrayHasKey('body', $parsed, 'The parsed result should contain a body key');
        $this->assertInternalType('string', $parsed['body'], "The body should be a string");
    }

    /**
     * @covers DNSMadeEasy\driver\Response::parseMessage
     */
    public function testParseInvalidMessage()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $parseMessage = $reflectionClass->getMethod('parseMessage');
        $parseMessage->setAccessible(true);

        try {
            $parsed = $parseMessage->invoke($this->response, "invalid data");
        } catch (\Exception $e) {
            $this->assertInstanceOf('DNSMadeEasy\exception\RESTException', $e, 'Exception thrown was not a DNSMadeEasy\exception\RESTException');

            return;
        }

        $this->fail('Tried to parse an invalid http response, but no exception was thrown');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::parseHeaders
     */
    public function testParseHeaders()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $parseHeaders = $reflectionClass->getMethod('parseHeaders');
        $parseHeaders->setAccessible(true);

        $parseMessage = $reflectionClass->getMethod('parseMessage');
        $parseMessage->setAccessible(true);

        $body = $parseMessage->invoke($this->response, $this->responseData)['headers'];

        $parsed = $parseHeaders->invoke($this->response, $body);

        $this->assertInternalType('array', $parsed, 'The parsed result should be an array');

        $this->assertArrayHasKey('version', $parsed, 'The parsed result should contain a version element');
        $this->assertEquals('1.1', $parsed['version'], 'The parsed version does not match');

        $this->assertArrayHasKey('statusCode', $parsed, 'The parsed result should contain a statusCode element');
        $this->assertEquals(200, $parsed['statusCode'], 'The parsed statusCode does not match');

        $this->assertArrayHasKey('headers', $parsed, 'The parsed result should contain a headers element');
        $this->assertInternalType('array', $parsed['headers'], 'The parsed headers should be an array');
        $this->assertNotEmpty($parsed['headers'], 'The headers array should not be empty');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::fixJSON
     */
    public function testFixJSON()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $fixJSON = $reflectionClass->getMethod('fixJSON');
        $fixJSON->setAccessible(true);

        $invalidData = '{error: ["errormessage1"]}';

        $fixed = $fixJSON->invoke($this->response, $invalidData);

        $this->assertEquals('{"error": ["errormessage1"]}', $fixed, 'The invalid JSON was not fixed properly');
    }

    /**
     * @covers DNSMadeEasy\driver\Response::fixJSON
     */
    public function testFixJSONWithValidJSON()
    {
        $reflectionClass = new \ReflectionClass('DNSMadeEasy\driver\Response');

        $fixJSON = $reflectionClass->getMethod('fixJSON');
        $fixJSON->setAccessible(true);

        $validData = '{"error": ["errormessage1"]}';

        $fixed = $fixJSON->invoke($this->response, $validData);

        $this->assertEquals($validData, $fixed, 'The valid JSON was mangled while trying to fix it');
    }
}
