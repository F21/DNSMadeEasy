<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Secondary dns
 * Performs actions on secondary dns domains in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Secondary
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
     * Get all secondary dns domains.
     * @param  integer             $amount An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page   An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($amount = null, $page = null )
    {
        return $this->_driver->get('/dns/secondary{?rows,page}', array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get a secondary dns domain.
     * @param  integer             $id The id of the secondary dns domain.
     * @return \DNSMadeEasy\Result
     */
    public function get($id)
    {
        return $this->_driver->get("/dns/secondary/$id");
    }

    /**
     * Add a secondary dns domain.
     * @param  array|string        $domain If domain is an array, add multiple domains. If it is a string, just add one domain.
     * @param  array               $config An array of configuration to apply to the domain or domains.
     * @return \DNSMadeEasy\Result
     */
    public function add($domain, array $config = array())
    {
        if (is_array($domain)) {
            $data = array('names' => $domain);
        } else {
            $data = array('names' => array($domain));
        }

        $data = array_merge($data, $config);

        return $this->_driver->post("/dns/secondary/", $data);
    }

    /**
     * Delete one or multiple secondary dns domains.
     * @param  array|integer       $id An array of domain ids if deleting multiple domains or an integer if deleting one domain.
     * @return \DNSMadeEasy\Result
     */
    public function delete($id)
    {
        if (is_array($id)) {
            $data = $id;
        } else {
            $data = array($id);
        }

        return $this->_driver->delete("/dns/secondary/", $data);
    }

    /**
     * Update a secondary dns domain by its id.
     * @param array|integer $id     An array of domain ids if updating multiple domains or an integer if updating one domain.
     * @param array         $config
     */
    public function update($id, array $config)
    {
        if (is_array($id)) {
            $data = array('ids' => $id);
        } else {
            $data = array('ids' => array($id));
        }

        $data = array_merge($data, $config);

        return $this->_driver->put("/dns/secondary", $data);
    }
}
