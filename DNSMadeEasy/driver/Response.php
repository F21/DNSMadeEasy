<?php
namespace DNSMadeEasy\driver;
use DNSMadeEasy\exception\RESTException;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Response
 * The driver response object.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Response
{
    /**
     * The raw response header string.
     * @var string.
     */
    private $_rawHeaders;

    /**
     * The HTTP version.
     * @var string
     */
    private $_version;

    /**
     * The response status code.
     * @var integer
     */
    private $_statusCode;

    /**
     * The array of processed headers (in key-value pairs).
     * @var array
     */
    private $_headers = array();

    /**
     * The response body.
     * @var null|string
     */
    private $_body;

    /**
     * The time taken for the request in seconds.
     * @var float
     */
    private $_timeTaken;

    /**
     * Definitions for the HTTP codes.
     * @var array
     */
    private $_httpCodeDefinitions = array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            418 => 'I\'m a teapot',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
    );

    /**
     * These methods won't receive a response body
     * @var array
     */
    private $_httpMethodsWithoutBody = array(
        'delete',
        'put',
    );

    /**
     * Construct the driver response.
     * @param string $response  The response containing the headers and body as a string.
     * @param float  $timeTaken The time taken in seconds.
     */
    public function __construct($response, $timeTaken, $method = false)
    {
        $this->_timeTaken = $timeTaken;

        $parsed = $this->parseMessage($response, $method);

        $this->_rawHeaders = $parsed['headers'];
        $this->_body = trim($this->fixJSON($parsed['body']));

        $parsedHeaders = $this->parseHeaders($parsed['headers']);

        $this->_statusCode = (int) $parsedHeaders['statusCode'];
        $this->_version = $parsedHeaders['version'];
        $this->_headers = $parsedHeaders['headers'];
    }

    /**
     * Get the HTTP version.
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get the status code.
     * @return integer
     */
    public function getStatusCode()
    {
        return $this->_statusCode;
    }

    /**
     * Get the status description.
     * @return string
     */
    public function getStatus()
    {
        return $this->_httpCodeDefinitions[$this->_statusCode];
    }

    /**
     * Get the processed headers in key-value pairs.
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get the response body.
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Get the time taken for the request in seconds.
     * @return float
     */
    public function getTimeTaken()
    {
        return $this->_timeTaken;
    }

    /**
     * Get the raw headers as a string.
     * @return string
     */
    public function getRawHeaders()
    {
        return $this->_rawHeaders;
    }

    /**
     * Parse the response message to split it into the headers and the body.
     * @param  string        $message The response string.
     * @throws RESTException
     * @return array
     */
    private function parseMessage($message, $method)
    {
        assert(is_string($message));

        /**
         * when you do bulk records insert DnsMadeEasy adds "HTTP/1.1 100 Continue\n\n"
         *  in front of other headers, which breaks everything else
        **/
        $message = trim(preg_replace('/^HTTP\/1.1 100 Continue[\n\r]/', '', $message));

        $barrier = "\r\n\r\n";
        $border  = strpos($message, $barrier);

        if ($border === false) {
	    if (in_array($method,$this->_httpMethodsWithoutBody)) {
                $border = strlen($message);
            } else {
                throw new RESTException('Got an invalid response from the server.');
            }
        }

        $result = array();

        $result['headers'] = substr($message, 0, $border);
        $result['body']   = substr($message, $border + strlen($barrier));

        return $result;
    }

    /**
     * Parse the header string into key-value pairs.
     * @param  string $headers The headers string.
     * @return array
     */
    private function parseHeaders($headers)
    {
        $processed = array();

        foreach (explode("\r\n", $headers) as $lineNumber => $line) {
            $line = trim($line);

            //The first line is special and not a key-value pair.
            if ($lineNumber == 0) {

                $pattern='/^HTTP\/(\S+)\s*(\d+)/i';
                $matches = array();
                preg_match($pattern, $line, $matches);
                array_shift($matches);
                list($version, $statusCode) = $matches;

                $processed['version'] = $version;
                $processed['statusCode'] = $statusCode;

            //Other lines contain key:value-like headers
            } else {

                list($key, $value) = explode(':', $line, 2);
                $processed['headers'][trim($key)] = trim($value);
            }
        }

        return $processed;
    }

    /**
     * Sometimes, the API returns malformed JSON, so we need to fix it here.
     * If the JSON is fine, the string is returned as i
     * @param  string $badJSON The malformed JSON.
     * @return mixed
     */
    private function fixJSON($badJSON)
    {
        if (!json_decode($badJSON)) {
            $badJSON = preg_replace('/(\w+):/i', '"\1":', $badJSON);
        }

        return $badJSON;
    }
}
