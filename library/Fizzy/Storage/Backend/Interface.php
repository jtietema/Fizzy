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
 * Interface for Fizzy storage backends. This interface must be implemented by
 * all available storage backends to ensure the storage class can connect to
 * all backends.
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
    public function persist($container, $data, $identifier = null);

    /**
     * Delete an item in a storage container.
     *
     * @param string $container The name of the container the item is stored in.
     * @param string $identifier The identifier for the item to delete.
     * @return boolean
     */
    public function delete($container, $identifier);

    /**
     * Fetch all items from a storage container. The keys for the items are the
     * identifiers for the item values.
     *
     * @param string $container The container to fetch the items from.
     * @return array
     */
    public function fetchAll($container);

    /**
     * Fetch one item from a storage container by it's identifier. Implementation
     * must return an array with one item where the key of the item is the
     * identifier for the value of the item.
     *
     * @param string $container The container to fetch the item from.
     * @param string $identifier The identifier for the item to fetch.
     * @return array|null
     */
    public function fetchByIdentifier($container, $identifier);

    /**
     * Fetches items from a storage container by the specified columns. The keys
     * for the items are the identifiers for the item values.
     *
     * @param string $container The container to fetch the items from.
     * @param array $columns The columns and values to match.
     * @return array
     */
    public function fetchByColumn($container, array $columns);

}
