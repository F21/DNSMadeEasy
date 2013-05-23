<?php
namespace DNSMadeEasy\driver;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Request
 * Represents an object sent to the server.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Request
{
    /**
     * The raw headers.
     * @var string
     */
    private $_rawHeaders;

    /**
     * The request method.
     * @var string
     */
    private $_method;

    /**
     * The HTTP version.
     * @var string
     */
    private $_version;

    /**
     * The request URL.
     * @var string
     */
    private $_url;

    /**
     * Decoded and processed headers in key-value pairs..
     * @var array
     */
    private $_headers = array();

    /**
     * The body of the request.
     * @var unknown
     */
    private $_body;

    /**
     * Constructs the request object.
     * @param array  $info Information about the request from CURL.
     * @param string $body The request body.
     */
    public function __construct(array $info, $body = null)
    {
        $this->_rawHeaders = trim($info['request_header']);
        $this->_body = $body;

        $parsed = $this->parseHeaders($info['request_header']);

        $this->_method = $parsed['method'];
        $this->_version = $parsed['version'];
        $this->_url = $parsed['url'];
        $this->_headers = $parsed['headers'];
    }

    /**
     * Get the request method.
     * @return string
     */
    public function getMethod()
    {
        return $this->_method;
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
     * Get the request URL.
     * @return string
     */
    public function getUrl()
    {
        return $this->_url;
    }

    /**
     * Get the processed headers.
     * @return array
     */
    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * Get the request body.
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * Get the raw request headers.
     * @return string
     */
    public function getRawHeaders()
    {
        return $this->_rawHeaders;
    }

    /**
     * Parse the request headers string.
     * @param  string $headers
     * @return array
     */
    private function parseHeaders($headers)
    {
        $processed = array();

        foreach (explode("\r\n", trim($headers)) as $lineNumber => $line) {
            $line = trim($line);

            //First line is special and not a key-value pair.
            if ($lineNumber == 0) {

                $pattern='/^(GET|POST|PUT|DELETE|HEAD|TRACE|OPTIONS)\s+(\S+)\s+HTTP\/(\S+)/';
                $matches = array();
                preg_match($pattern, $line, $matches);
                array_shift($matches);
                list($method, $url, $version) = $matches;

                $processed['method'] = $method;
                $processed['url'] = $url;
                $processed['version'] = $version;

            //Other lines contain key:value-like headers
            } else {

                list($key, $value) = explode(':', $line, 2);
                $processed['headers'][trim($key)] = trim($value);
            }
        }

        return $processed;
    }
}
