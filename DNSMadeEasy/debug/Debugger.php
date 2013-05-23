<?php
namespace DNSMadeEasy\debug;
use DNSMadeEasy\driver\Request;
use DNSMadeEasy\driver\Response;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Debugger
 * Outputs the request and response to the browser. Since the debugger outputs HTML, it's best to use it when you are
 * testing using the browser.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Debugger
{

    /**
     * Debug a request.
     * @param Request $request The driver request object.
     */
    public function request(Request $request)
    {
        print '<table style="border-style: solid; border-width: 1px; border-color: #b1b1b1; margin: 5px"><tr><td style="vertical-align: top; padding: 10px; width: 400px">';
        print "<h3>Request to server</h3>";
        print "<p><strong>{$request->getMethod()}</strong> {$request->getURL()}</p>";

        //Headers
        print "<pre>";
        foreach ($request->getHeaders() as $header => $value) {
            print "$header : $value\n";
        }
        print "</pre>";

        //Body
        if ($request->getBody()) {
            print "<pre>" . json_encode($request->getBody(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</pre>";
        }

        print '</td>';
    }

    /**
     * Debug a response.
     * @param Response $response The driver response object.
     */
    public function response(Response $response)
    {
        print '<td style="vertical-align: top; padding: 10px; padding-left: 20px; width: 400px"><h3>Response from server</h3>';

        if ($response->getStatusCode() >= 400) {
            print '<p style="color: red"><strong>' . $response->getStatusCode() . '</strong> ' . $response->getStatus() . '</p>';
        } else {
            print '<p style="color: green"><strong>' . $response->getStatusCode() . '</strong> ' . $response->getStatus() . '</p>';
        }

        //Headers
        print "<pre>";
        foreach ($response->getHeaders() as $header => $value) {
            print "$header : $value\n";
        }
        print "</pre>";

        //Body
        if ($response->getBody()) {
            $body = json_decode($response->getBody());

            if ($body) {
                print "<pre>" . json_encode($body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</pre>";
            } elseif ($response->getBody()) {
                print "<pre>" . $response->getBody() . "</pre>";
            }

        }

        print '<p>Time taken: ' . $response->getTimeTaken() * 1000 . ' ms</pre>';
        print "</td></tr></table>";
    }
}
