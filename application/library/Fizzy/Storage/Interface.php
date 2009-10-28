<?php
/**
 * Class Fizzy_Storage_Interface
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
 */
interface Fizzy_Storage_Interface
{
    
    public function __construct($dsn);

    /**
     * Persist the given model (add or save).
     * Returns the persisted model (with added id).
     *
     * @param Fizzy_Storage_Model $model
     * @return Fizzy_Storage_Model
     */
    public function persist(Fizzy_Storage_Model $model);

    /**
     * Remove the given model from persistence.
     *
     * @param Fizzy_Storage_Model $model
     */
    public function remove(Fizzy_Storage_Model $model);

    /**
     * Fetch one item of $type with $uid.
     *
     * @param string $type
     * @param mixed $uid
     * @return Fizzy_Storage_Model|null
     */
    public function fetchOne($type, $uid);

    /**
     * Fetch all entities from a specific type (e.g. pages, users).
     *
     * @param string $type
     * @return array
     */
    public function fetchAll($type);

    /**
     * Used to select a row by a Value in a specific column
     *
     * @param string $type
     * @param string $column
     * @param mixed $value
     * @return Fizzy_Storage_Model|null
     */
    public function fetchColumn($type, $column, $value);
}
