<?php
namespace DNSMadeEasy;

/**
 * DNSMadeEasy is a PHP library to talk with DNSMadeEasy's v2.0 REST API.
 * This is a low level library that allows you to perform operations against the API and receieve a result object.
 * It also contains all tested methods (some are missing from DME's documentation) and deals with issues like bad/malformed data or
 * JSON being returned.
 *
 * Autoloader
 * Performs autoloading for the libray. Since the class files are organised to PSR-0 standards, you can use your own autoloader
 * if desired.
 *
 * @version 1.0.0
 *
 * @author Francis Chuang <francis.chuang@gmail.com>
 * @link https://github.com/F21/DNSMadeEasy
 * @license http://www.apache.org/licenses/LICENSE-2.0.html Apache 2 License
 */
class Autoloader
{
    /**
     * Class file extension.
     * @var string
     */
    const EXTENSION = '.php';

    /**
     * The directory root of the library.
     * @var string
     */
    private static $dirRoot = null;

    /**
     * Initialise the autoloader
     *
     * @throws Exception
     * @return void
     */
    public static function init()
    {
        spl_autoload_register(__NAMESPACE__ . '\Autoloader::load');

        self::$dirRoot = dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    /**
     * Loads a class file.
     * @param string $className The name of the class..
     */
    public static function load($className)
    {
        $className = str_replace(__NAMESPACE__, '', $className);
        $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);

        if (file_exists(self::$dirRoot . $className . self::EXTENSION)) {
            require_once self::$dirRoot . $className . self::EXTENSION;
        }
    }
}
