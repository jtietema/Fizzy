<?php
/**
 * Class Fizzy_Storage_Backend_Sqlite
 * @package Fizzy
 * @subpackage Storage
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.voidwalkers.nl/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@voidwalkers.nl so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2009 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

/** Fizzy_Storage_Backend_Pdo */
require_once 'Fizzy/Storage/Backend/Pdo.php';

/**
 * Storage backend based on PDO_SQLite
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
class Fizzy_Storage_Backend_Sqlite extends Fizzy_Storage_Backend_Pdo
{
    /**
     * Constructor. Will check the dsn for relative paths and prefix them with
     * the application base path.
     * @param array $options
     */
    public function __construct($options = array())
    {
        parent::__construct($options);

        $dsn = $this->getDsn();
        list($protocol, $dataPath) = explode(':', $dsn);

        // Change path to system directory separator
        $dataPath = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $dataPath);
        // Add a trailing slash
        $dataPath = rtrim($dataPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        // Check if path is relative within the web application root
        if(0 !== strpos($dataPath, DIRECTORY_SEPARATOR))
        {
            $basePath = Fizzy_Config::getInstance()->getSectionValue(Fizzy_Config::SECTION_APPLICATION, 'basePath');
            $dataPath = $basePath . $dataPath;
        }

        // Set the dsn with the corrected absolute or relative path
        $this->setDsn($protocol . ':' . $dataPath);
    }
}