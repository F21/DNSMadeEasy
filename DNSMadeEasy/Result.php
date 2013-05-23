<?php
namespace DNSMadeEasy;
use DNSMadeEasy\driver\Response;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Result Object
 * This object is returned for every request sent to the server. It provides a unified way to determine whether the request
 * was successful or not, provides other meta data such as the request limits and the response from the server.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Result
{
    /**
     * Whether the request is successful or not.
     * @var boolean
     */
    public $success = true;

    /**
     * The HTTP status code.
     * @var integer
     */
    public $statusCode = null;

    /**
     * An array of errors from DME's API if errors have occurred.
     * @var array
     */
    public $errors = null;

    /**
     * The id of the request.
     * @var string
     */
    public $requestId;

    /**
     * The number of requests remaining.
     * @var integer
     */
    public $requestsRemaining;

    /**
     * The request limit.
     * @var integer
     */
    public $requestLimit;

    /**
     * The decoded body of the response.
     * @var object|string|null
     */
    public $body;

    /**
     * Constructs the result object.
     * @param Response $response The driver response object.
     */
    public function __construct(Response $response)
    {
        $decoded = json_decode($response->getBody());

        //If things did not go well.
        if ($response->getStatusCode() >= 400 && $response->getStatusCode() < 600) {
            $this->success = false;

            if (isset($decoded->error)) {
                $this->errors = $decoded->error;
            }

            if (!$this->errors) {
                // API calls don't always provide an error :(
                $this->errors[] = 'An error occurred, however, no error message was given. Use the response body, HTTP status code and URL to help troubleshoot the issue.';
            }

        //If things went well.
        } else {
            $this->body = $decoded;
        }

        $this->statusCode = $response->getStatusCode();

        //DME's request limit meta data.
        if (isset($response->getHeaders()['x-dnsme-requestId'])) {
            $this->requestId = $response->getHeaders()['x-dnsme-requestId'];
        }

        if (isset($response->getHeaders()['x-dnsme-requestsRemaining'])) {
            $this->requestsRemaining = $response->getHeaders()['x-dnsme-requestsRemaining'];
        }

        if (isset($response->getHeaders()['x-dnsme-requestLimit'])) {
            $this->requestLimit = $response->getHeaders()['x-dnsme-requestLimit'];
        }

    }
}
