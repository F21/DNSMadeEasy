<?php
namespace tests\DNSMadeEasy;
use tests\Base;
use DNSMadeEasy\Result;
use DNSMadeEasy\driver\Response;

/**
 * Tests for the result object.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class ResultTest extends Base
{
    /**
     * An instance of the response object.
     * @var Response
     */
    protected $response;

    /**
     * An instance of an error response.
     * @var Response
     */
    protected $errorResponse;

    /**
     * An instance of an error response without an error message.
     * @var Response
     */
    protected $errorResponseWithNoErrorMessage;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $responseData =
        "HTTP/1.1 200 OK\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n
{\"data\":[{\"name\":\"test.com\",\"id\":861223,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"vanityId\":12241,\"created\":1369094400000,\"updated\":1369139723297,\"processMulti\":false},{\"name\":\"test1.com\",\"id\":861221,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"created\":1369094400000,\"updated\":1369139430632,\"processMulti\":false}],\"page\":0,\"totalPages\":1,\"totalRecords\":2}";

        $errorResponseData =
        "HTTP/1.1 404 Not Found\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n{error: [\"An error occurred\"]}";

        $errorResponseDataNoMessage =
        "HTTP/1.1 404 Not Found\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n";

        $timeTaken = 1.1;

        $this->response = new Response($responseData, $timeTaken);

        $this->errorResponse = new Response($errorResponseData, $timeTaken);

        $this->errorResponseWithNoErrorMessage = new Response($errorResponseDataNoMessage, $timeTaken);
    }

    /**
     * @covers DNSMadeEasy\Result::__construct
     */
    public function testConstructor()
    {
        $result = new Result($this->response);

        $this->assertTrue($result->success, "The result's sucess property should be true");
        $this->assertEquals(200, $result->statusCode, "The status code should be 200");
        $this->assertEmpty($result->errors, "There should be no errors");
        $this->assertEquals('918f36f1-45f1-4bdc-8d55-a6f26a3db73b', $result->requestId, "The request id does not match");
        $this->assertEquals(148, $result->requestsRemaining, "The requests remaining does not match");
        $this->assertEquals(150, $result->requestLimit, "The request limit does not match");
        $this->assertInstanceOf('stdClass', $result->body, "The body should be an instance of stdClass");
        $this->assertEquals(json_decode("{\"data\":[{\"name\":\"test.com\",\"id\":861223,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"vanityId\":12241,\"created\":1369094400000,\"updated\":1369139723297,\"processMulti\":false},{\"name\":\"test1.com\",\"id\":861221,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"created\":1369094400000,\"updated\":1369139430632,\"processMulti\":false}],\"page\":0,\"totalPages\":1,\"totalRecords\":2}"),
                            $result->body, "The decoded body does not match");
    }

    /**
     * @covers DNSMadeEasy\Result::__construct
     */
    public function testConstructorForUnsuccessfulResult()
    {
        $result = new Result($this->errorResponse);

        $this->assertFalse($result->success, "The result's sucess property should be false");
        $this->assertEquals(404, $result->statusCode, "The status code should be 404");
        $this->assertNotEmpty($result->errors, "There should be an error");
        $this->assertCount(1, $result->errors, "There should be 1 error");
        $this->assertEquals('918f36f1-45f1-4bdc-8d55-a6f26a3db73b', $result->requestId, "The request id does not match");
        $this->assertEquals(148, $result->requestsRemaining, "The requests remaining does not match");
        $this->assertEquals(150, $result->requestLimit, "The request limit does not match");
        $this->assertNull($result->body, "The body should be null");
    }

    /**
     * @covers DNSMadeEasy\Result::__construct
     */
    public function testConstructorForUnsuccessfulResultWithNoErrorMessage()
    {
        $result = new Result($this->errorResponseWithNoErrorMessage);

        $this->assertFalse($result->success, "The result's sucess property should be false");
        $this->assertEquals(404, $result->statusCode, "The status code should be 404");
        $this->assertNotEmpty($result->errors, "There should be an error");
        $this->assertCount(1, $result->errors, "There should be 1 error");
        $this->assertEquals('An error occurred, however, no error message was given. Use the response body, HTTP status code and URL to help troubleshoot the issue.',
                            $result->errors[0], "The error message does not match");
        $this->assertEquals('918f36f1-45f1-4bdc-8d55-a6f26a3db73b', $result->requestId, "The request id does not match");
        $this->assertEquals(148, $result->requestsRemaining, "The requests remaining does not match");
        $this->assertEquals(150, $result->requestLimit, "The request limit does not match");
        $this->assertNull($result->body, "The body should be null");
    }
}
