<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Records
 * Performs actions on records in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Records
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the records manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all records for a domain.
     * @param  integer             $domainId The id of the domain.
     * @param  string              $type     An optional filter to return only records of a certain type.
     * @param  integer             $amount   An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page     An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($domainId, $type = null, $amount = null, $page = null)
    {
        return $this->_driver->get("/dns/managed/$domainId/records{?type,rows,page}", array('type' => $type, 'rows' => $amount, 'page' => $page));
    }

    /**
     * Get a record for a domain.
     * @param  integer             $domainId The id of the domain.
     * @param  integer             $recordId The id of the record.
     * @return \DNSMadeEasy\Result
     */
    public function get($domainId, $recordId)
    {
        return $this->_driver->get("/dns/managed/$domainId/records/$recordId");
    }

    /**
     * Add a record to a domain.
     * To insert multiple records at once, $config should be an array containing array of records.
     * @param  integer             $domainId The id of the domain.
     * @param  array               $config   The configuration for the record.
     * @return \DNSMadeEasy\Result
     */
    public function add($domainId, array $config)
    {
    	
    	$default = array('ttl' => 1800, 'gtdLocation' => 'DEFAULT');
    	 
    	if(is_array(reset($config))){
    		
    		foreach ($config as &$record) {
    			$record = array_merge($default, $record);
    		}
    		
    		return $this->_driver->post("/dns/managed/$domainId/records/createMulti", $config);
    		
    	}else{
    		
    		$config = array_merge($default, $config);
    		return $this->_driver->post("/dns/managed/$domainId/records", $config);
    	}
    }

    /**
     * Delete records from a domain.
     * @param  integer             $domainId The id of the domain.
     * @param  array|integer       $recordId If deleting multiple records, an array of record ids, otherwise just the record id to delete a single record.
     * @return \DNSMadeEasy\Result
     */
    public function delete($domainId, $recordId)
    {
        if (is_array($recordId)) {
            return $this->_driver->delete("/dns/managed/$domainId/records?ids=" . implode($recordId, '&ids='));
        } else {
            return $this->_driver->delete("/dns/managed/$domainId/records/$recordId");
        }
    }

    /**
     * Update a record.
     * To update multiple records at once, $data should be an array containing array of records and $recordId is not needed.
     * @param  integer             $domainId The id of the domain.
     * @param  integer             $recordId The id of the record.
     * @param  array               $data     The new configuration for the record.
     * @return \DNSMadeEasy\Result
     */
    public function update($domainId, array $data, $recordId = null)
    {
    	if(is_array(reset($data))){
    		return $this->_driver->put("/dns/managed/$domainId/records/updateMulti", $data);
    	}else{
    		return $this->_driver->put("/dns/managed/$domainId/records/$recordId", $data);
    	}
        
    }
}
