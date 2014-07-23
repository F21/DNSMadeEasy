<?php
namespace DNSMadeEasy\driver;
use DNSMadeEasy\debug\Debugger;
use DNSMadeEasy\Result;
use DNSMadeEasy\exception\RESTException;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * REST driver
 * Makes the call to the server and returns a result object.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class REST
{
    /**
     * The configuration object.
     * @var Configuration
     */
    private $_config;

    /**
     * The debugger.
     * @var Debugger
     */
    private $_debugger;

    /**
     * The URI template processor.
     * @var URITemplate
     */
    private $_uriTemplate;

    /**
     * Constructs the driver.
     * @param Configuration $config The configuration object.
     */
    public function __construct(Configuration $config)
    {
        $this->_config = $config;
        $this->_debugger = new Debugger();
        $this->_uriTemplate = new URITemplate();
    }

    /**
     * Performs a GET.
     * @param  string              $command       The URI to append after the base URL.
     * @param  array               $uriParameters An optional array of URI template parameters.
     * @return \DNSMadeEasy\Result
     */
    public function get($command, array $uriParameters = array())
    {
        return $this->send($command, $uriParameters, 'get');
    }

    /**
     * Performs a POST.
     * @param  string              $command       The URI to append after the base URL.
     * @param  string              $content       The request body.
     * @param  array               $uriParameters An optional array of URI template parameters.
     * @return \DNSMadeEasy\Result
     */
    public function post($command, $content = null, array $uriParameters = array())
    {
        return $this->send($command, $uriParameters, 'post', $content);
    }

    /**
     * Performs a PUT.
     * @param  string              $command       The URI to append after the base URL.
     * @param  string              $content       The request body.
     * @param  array               $uriParameters An optional array of URI template parameters.
     * @return \DNSMadeEasy\Result
     */
    public function put($command, $content = null, array $uriParameters = array())
    {
        return $this->send($command, $uriParameters, 'put', $content);
    }

    /**
     * Performs a DELETE.
     * @param  string              $command       The URI to append after the base URL.
     * @param  string              $content       The request body.
     * @param  array               $uriParameters An optional array of URI template parameters.
     * @return \DNSMadeEasy\Result
     */
    public function delete($command, $content = null, array $uriParameters = array())
    {
        return $this->send($command, $uriParameters, 'delete', $content);
    }

    /**
     * Adds the authentication header to the request.
     * @param resource $ch The CURL handle.
     */
    private function getAuthenticationHeaders()
    {
        if (!$this->_config->getSecretKey() || !$this->_config->getAPIKey()) {
            throw new RESTException("An API Key and Secret Key is required to make calls to the api.");
        }

        $date = date_create("now", new \DateTimeZone("GMT"))->format('D, d M Y H:i:s e');
        $hash = hash_hmac("sha1", $date, $this->_config->getSecretKey());

        return array("x-dnsme-apiKey: {$this->_config->getAPIKey()}",
                     "x-dnsme-requestDate: $date",
                     "x-dnsme-hmac: $hash"
                  );
    }

    /**
     * Sends the request to the server.
     * @param  string              $command       The URI to append after the base URL.
     * @param  array               $uriParameters An optional array of URI template parameters.
     * @param  string              $method        The request method.
     * @param  string              $content       The request body.
     * @throws RESTException
     * @return \DNSMadeEasy\Result
     */
    private function send($command, $uriParameters, $method, $content = NULL)
    {
        $url = $this->_config->getURL();

        $ch = curl_init($url . $this->_uriTemplate->expand($command, $uriParameters));

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getAuthenticationHeaders());

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($content !== NULL) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($content));
        }

        switch (strtolower($method)) {

            case "get":
                curl_setopt($ch, CURLOPT_HTTPGET, true);
                break;

            case "post":
                curl_setopt($ch, CURLOPT_POST, true);
                break;

            case "put":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); //CURL_OPTPUT requires INFILE and writing to ram/disk, which is annoying
                break;

            case "delete":
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
        }

        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        if($this->_config->usingSandbox()){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //Temporary workaround because https://api.sandbox.dnsmadeeasy.com uses a bad certificate
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }else{
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        }

        $result = curl_exec($ch);

        //Something bad has happened, for example the API is down, etc.
        if (curl_errno($ch)) {
            $errorNumber = curl_errno($ch);
            $error = curl_error($ch);
            throw new RESTException("Error: $errorNumber - $error");
        }

        $info = curl_getinfo($ch);
        $request = new Request($info, $content);
        $response = new Response($result, $info['total_time']);

        //If debug mode is on, output the debug messages.
        if ($this->_config->getDebug()) {
            $this->_debugger->request($request);
            $this->_debugger->response($response);
        }

        curl_close($ch);

        return new Result($response);
    }
}
