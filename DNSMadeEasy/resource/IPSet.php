<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * IPSet
 * Performs actions on ip sets in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class IPSet
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the domains manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all ip sets.
     * @param  integer             $amount An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page   An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($amount = null, $page = null )
    {
        return $this->_driver->get('/dns/secondary/ipSet{?rows,page}', array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get an ip set by its id.
     * @param  integer             $id The id of the ip set.
     * @return \DNSMadeEasy\Result
     */
    public function get($id)
    {
        return $this->_driver->get("/dns/secondary/ipSet/$id");
    }

    /**
     * Create an ip set.
     * @param array $config The configuration of the ip set.
     */
    public function add(array $config)
    {
        return $this->_driver->post("/dns/secondary/ipSet", $config);
    }

    /**
     * Delete an ip set by its id.
     * @param  integer             $id The id of the ip set.
     * @return \DNSMadeEasy\Result
     */
    public function delete($id)
    {
        if (is_array($id)) {
            $data = $id;
        } else {
            $data = array($id);
        }

        return $this->_driver->delete("/dns/secondary/ipSet", $data);
    }

    /**
     * Update an ip set.
     * @param  integer             $id     The id of the ip set.
     * @param  array               $config The new configuration of the ip set.
     * @return \DNSMadeEasy\Result
     */
    public function update($id, array $config)
    {
        return $this->_driver->put("/dns/secondary/ipSet/$id", $config);
    }
}
