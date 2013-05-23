<?php
namespace tests\DNSMadeEasy\debug;
use tests\Base;
use DNSMadeEasy\debug\Debugger;
use DNSMadeEasy\driver\Request;
use DNSMadeEasy\driver\Response;

/**
 * Tests for the debugger.
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class DebuggerTest extends Base
{
    /**
     * An instance of the debugger.
     * @var Debugger
     */
    protected $debugger;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->debugger = new Debugger();
    }

    /**
     * @covers DNSMadeEasy\debug\Debugger::request
     */
    public function testRequest()
    {
        $curlInfo = array('url' => 'http://api.sandbox.dnsmadeeasy.com/V2.0/dns/managed',
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

        $data = json_encode(array('names' => array('test.com')));

        $request = new Request($curlInfo, $data);

        $this->setOutputCallback(function($output){
            $this->assertInternalType('string', $output);
        });

        //Fire off the debug request
        $this->debugger->request($request);
    }

    /**
     * @covers DNSMadeEasy\debug\Debugger::response
     */
    public function testResponse()
    {
        $response =
"HTTP/1.1 200 OK\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n
{\"data\":[{\"name\":\"test.com\",\"id\":861223,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"vanityId\":12241,\"created\":1369094400000,\"updated\":1369139723297,\"processMulti\":false},{\"name\":\"test1.com\",\"id\":861221,\"folderId\":1027,\"pendingActionId\":0,\"gtdEnabled\":false,\"created\":1369094400000,\"updated\":1369139430632,\"processMulti\":false}],\"page\":0,\"totalPages\":1,\"totalRecords\":2}";

        $response = new Response($response, 1.2);

        $this->setOutputCallback(function($output){
            $this->assertInternalType('string', $output);
        });

        //Fire off the debug request
        $this->debugger->response($response);
    }

    /**
     * @covers DNSMadeEasy\debug\Debugger::response
     */
    public function testResponseWithFailure()
    {
        $response =
        "HTTP/1.1 400 Bad Request\r\nServer: Apache-Coyote/1.1\r\nx-dnsme-requestId: 918f36f1-45f1-4bdc-8d55-a6f26a3db73b\r\nx-dnsme-requestsRemaining: 148\r\nx-dnsme-requestLimit: 150\r\nSet-Cookie: JSESSIONID=3CC6DB92E45AD918WS894TSDDC67EF47; Path=/V2.0/; HttpOnly\r\nContent-Type: application/json\r\nTransfer-Encoding: chunked\r\nDate: Wed, 22 May 2013 06:37:08 GMT\r\n\r\n
Bad Request";

        $response = new Response($response, 1.2);

        $this->setOutputCallback(function($output){
            $this->assertInternalType('string', $output);
        });

            //Fire off the debug request
            $this->debugger->response($response);
    }
}
