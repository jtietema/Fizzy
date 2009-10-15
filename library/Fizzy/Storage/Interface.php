<?php

/**
 * Interface that all storage backends should implement.
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
interface Fizzy_Storage_Interface
{
    
    public function __construct($dsn);

    public function persist(Fizzy_Model $model);

    public function remove(Fizzy_Model $model);

    public function fetchOne($type, $uid);

    public function fetchAll($type);
}
