<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Failover
 * Manages the failover monitors in your acount.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Failover
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the failover manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get a failover monitor.
     * @param  integer             $id The id of the failover monitor.
     * @return \DNSMadeEasy\Result
     */
    public function get($id)
    {
        return $this->_driver->get("/monitor/$id");
    }

    /**
     * Delete a failover monitor.
     * @param  integer             $id The id of the failover monitor.
     * @return \DNSMadeEasy\Result
     */
    public function delete($id)
    {
        return $this->_driver->delete("/monitor/$id");
    }

    /**
     * Update a failover monitor.
     * @param  integer             $id   The id of the failover monitor.
     * @param  array               $data The new configuration of the failover monitor.
     * @return \DNSMadeEasy\Result
     */
    public function update($id, array $data)
    {
        return $this->_driver->put("/monitor/$id", $data);
    }
}
