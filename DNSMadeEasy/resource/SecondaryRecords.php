<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Secondary dns records
 * Performs actions on secondary dns records in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class SecondaryRecords
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the secondary dns records manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all secondary dns records for a secondary dns domain.
     * @param  integer             $domainId The id of the domain.
     * @param  integer             $amount   An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page     An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($domainId, $amount = null, $page = null)
    {
        return $this->_driver->get("/dns/secondary/$domainId/records{?rows,page}", array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get a record for a secondary dns domain.
     * @param  integer             $domainId The id of the secondary dns domain.
     * @param  integer             $recordId The id of the record.
     * @return \DNSMadeEasy\Result
     */
    public function get($domainId, $recordId)
    {
        return $this->_driver->get("/dns/secondary/$domainId/records/$recordId");
    }

    /**
     * Add a secondary dns record to a secondary dns domain.
     * @param  integer             $domainId The id of the domain.
     * @param  array               $config   The configuration for the record.
     * @return \DNSMadeEasy\Result
     */
    public function add($domainId, array $config)
    {
        return $this->_driver->post("/dns/secondary/$domainId/records", $config);
    }

    /**
     * Delete secondary dns records from a domain.
     * @param  integer             $domainId The id of the domain.
     * @param  integer             $recordId If deleting multiple records, an array of record ids, otherwise just the record id to delete a single record.
     * @return \DNSMadeEasy\Result
     */
    public function delete($domainId, $recordId)
    {
        if (is_array($recordId)) {
            return $this->_driver->delete("/dns/secondary/$domainId/records?ids=" . implode($recordId, '&ids='));
        } else {
            return $this->_driver->delete("/dns/secondary/$domainId/records/$recordId");
        }
    }

    /**
     * Update a secondary dns record.
     * @param  integer             $domainId The id of the domain.
     * @param  integer             $recordId The id of the record.
     * @param  array               $data     The new configuration for the record.
     * @return \DNSMadeEasy\Result
     */
    public function update($domainId, $recordId, array $data)
    {
        return $this->_driver->put("/dns/secondary/$domainId/records/$recordId", $data);
    }
}
