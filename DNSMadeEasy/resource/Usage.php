<?php
namespace DNSMadeEasy\resource;
use DNSMadeEasy\driver\REST;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Usage
 * Display usage of your DNSMadeEasy account.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Usage
{
    /**
     * The REST driver.
     * @var REST
     */
    private $_driver;

    /**
     * Constructs the usage manager.
     * @param REST $driver The rest driver.
     */
    public function __construct(REST $driver)
    {
        $this->_driver = $driver;
    }

    /**
     * Get all usage for the account.
     * @param  integer             $amount An optional parameter restricting the result to be x amount per page.
     * @param  integer             $page   An optional parameter to return the results on page y.
     * @return \DNSMadeEasy\Result
     */
    public function getAll($amount=null, $page=null)
    {
        return $this->_driver->get("/usageApi/queriesApi{?rows,page}", array('rows' => $amount, 'page' => $page));
    }

    /**
     * Get usage by month.
     * @param  integer             $year  The year.
     * @param  integer             $month The month.
     * @return \DNSMadeEasy\Result
     */
    public function getByMonth($year, $month)
    {
        return $this->_driver->get("/usageApi/queriesApi/$year/$month");
    }

    /**
     * Get usage for domain.
     * @param  integer             $year     The year.
     * @param  integer             $month    The month.
     * @param  integer             $domainId The id of the domain.
     * @return \DNSMadeEasy\Result
     */
    public function getByMonthForDomain($year, $month, $domainId)
    {
        return $this->_driver->get("/usageApi/queriesApi/$year/$month/managed/$domainId");
    }

    /**
     * Get usage for a secondary domain.
     * @param  integer             $year        The year.
     * @param  integer             $month       The month.
     * @param  unknown             $secondaryId The id of the secondary domain.
     * @return \DNSMadeEasy\Result
     */
    public function getByMonthForSecondary($year, $month, $secondaryId)
    {
        return $this->_driver->get("/usageApi/queriesApi/$year/$month/secondary/$secondaryId");
    }
}
