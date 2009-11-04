<?php
/**
 * Class Fizzy_Storage_Backend_Interface
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

/**
 * Interface that all storage backends should implement.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 * @author Mattijs Hoitink <mattijs@voidwalkers.nl>
 */
interface Fizzy_Storage_Backend_Interface
{
    
    /**
     * Persist the given model. Returns true on success, false on failure.
     *
     * @param string $container
     * @param mixed $data
     * @param string|null $identifierField
     * @return int|boolean Returns the data set id on success, false on failure
     */
    public function persist($container, $data, $identifierField = null);

    /**
     * Delete the given model from persistence.
     *
     * @param string $container
     * @param string $identifierField
     * @param string $identifierValue
     * @return boolean
     */
    public function delete($container, $identifierField, $identifierValue);

    /**
     * Fetch one item of $type with $uid.
     *
     * @param string $container
     * @param int $identifier
     * @return array|null
     */
    public function fetchOne($container, $identifier);

    /**
     * Fetch all entities from a specific type (e.g. pages, users).
     *
     * @param string $container
     * @return array
     */
    public function fetchAll($container);

    /**
     * Fetches model using the specified column and value.
     *
     * @param string $container
     * @param array $columns
     * @return array
     */
    public function fetchByColumn($container, array $columns);

    /**
     * Checks if the backend encountered any errors.
     * @return boolean
     */
    public function hasErrors();

    /**
     * Returns the error the backend encountered.
     * @return array
     */
    public function getErrors();
    
}
