<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Vanity DNS
 * Performs actions on Vanity DNS configurations in your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class VanityDNS
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the transfer ACL manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all vanity DNS configurations.
     * @param  integer             $amount An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page   An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($amount = null, $page = null)
    {
        return $this->_driver->get("/dns/vanity{?rows,page}", array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get a vanity DNS configuration by its id.
     * @param  integer             $id The id of the vanity DNS configuration.
     * @return \DNSMadeEasy\Result
     */
    public function get($id)
    {
        return $this->_driver->get("/dns/vanity/$id");
    }

    /**
     * Add a vanity DNS configuration.
     * @param  array               $config The configuration for the vanity DNS configuration.
     * @return \DNSMadeEasy\Result
     */
    public function add(array $config)
    {
        return $this->_driver->post("/dns/vanity", $config);
    }

    /**
     * Delete a vanity DNS configuration by its id.
     * @param  integer             $id The of the vanity DNS configuration.
     * @return \DNSMadeEasy\Result
     */
    public function delete($id)
    {
        return $this->_driver->delete("/dns/vanity/$id");
    }
    
    /**
     * Delete all vanity DNS configuration.
     * @return \DNSMadeEasy\Result
     */
    public function deleteAll()
    {
    	$vanityDNS = $this->getAll();
    	
    	$counter = 0;
    	
    	foreach ($vanityDNS->body->data as $config){
    		
    		if(!$config->public){
    			$counter++;
    			$this->delete($config->id); //This is inefficient, but DME does not provide a mass delete method.
    		}
    	}
    	 
    	return $counter > 0;
    }

    /**
     * Update a vanity DNS configuration.
     * @param  integer             $id   The id of the vanity DNS configuration.
     * @param  array               $data The new configuration data for vanity DNS configuration.
     * @return \DNSMadeEasy\Result
     */
    public function update($id, array $data)
    {
        return $this->_driver->put("/dns/vanity/$id", $data);
    }
}
