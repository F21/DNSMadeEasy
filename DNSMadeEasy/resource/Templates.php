<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Templates
 * Performs actions on templates in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Templates
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the template manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all templates.
     * @param  integer             $amount An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page   An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($amount = null, $page = null)
    {
        return $this->_driver->get("/dns/template{?rows,page}", array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get a template by its id.
     * @param  integer             $id The id of the template.
     * @return \DNSMadeEasy\Result
     */
    public function get($id)
    {
        return $this->_driver->get("/dns/template/$id");
    }

    /**
     * Create a new template.
     * @param  array               $config Configuraton for the template.
     * @return \DNSMadeEasy\Result
     */
    public function add(array $config)
    {
        return $this->_driver->post("/dns/template", $config);
    }

    /**
     * Delete a template by its id.
     * @param  integer             $id The id of the template.
     * @return \DNSMadeEasy\Result
     */
    public function delete($id)
    {
        return $this->_driver->delete("/dns/template/$id");
    }

    /**
     * Update a template.
     * @param  integer             $id   The id of the template.
     * @param  array               $data The new configuration for the template.
     * @return \DNSMadeEasy\Result
     */
    public function update($id, array $data)
    {
        return $this->_driver->put("/dns/template/$id", $data);
    }
}
