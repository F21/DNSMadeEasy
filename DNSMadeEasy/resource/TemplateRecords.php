<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Template records
 * Performs actions on template records in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class TemplateRecords
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the template records manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get records for a template by record type.
     * @param  integer             $templateId The id of the template.
     * @param  string              $type       The record type.
     * @param  integer             $amount     An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page       An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getByType($templateId, $type, $amount = null, $page = null)
    {
        return $this->_driver->get("/dns/template/$templateId/records{?type,rows,page}", array('type' => $type, 'amount' => $amount, 'page' => $page));
    }

    /**
     * Add a record to a template.
     * @param  integer             $templateId The id of the template.
     * @param  array               $config     The configuration of the new record.
     * @return \DNSMadeEasy\Result
     */
    public function add($templateId, array $config)
    {
        return $this->_driver->post("/dns/template/$templateId/records", $config);
    }

    /**
     * Delete records from a template.
     * @param  integer             $templateId The id of the template.
     * @param  array|integer       $recordId   If deleting multiple records, an array of record ids, otherwise just the record id to delete a single record.
     * @return \DNSMadeEasy\Result
     */
    public function delete($templateId, $recordId)
    {
        if (is_array($recordId)) {
            return $this->_driver->delete("/dns/template/$templateId/records?ids=" . implode($recordId, '&ids='));
        } else {
            return $this->_driver->delete("/dns/template/$templateId/records/$recordId");
        }
    }

    /**
     * Replace all the records in a template.
     * @param  array               $templateId The id of the template.
     * @param  array               $data       An array of new records.
     * @return \DNSMadeEasy\Result
     */
    public function replaceAllRecords($templateId, array $data)
    {
        return $this->_driver->put("/dns/template/$templateId/records", $data);
    }

    /**
     * Update a record in a template.
     * @param  integer             $template Id The id of the template.
     * @param  integer             $recordId The id of the record.
     * @param  array               $data     The new configuration for the record.
     * @return \DNSMadeEasy\Result
     */
    public function update($templateId, $recordId, array $data)
    {
        return $this->_driver->put("/dns/template/$templateId/records/$recordId", $data);
    }
}
