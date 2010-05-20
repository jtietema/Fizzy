<?php
/**
 * Class Fizzy_Paginator_Adapter_DoctrineQuery
 * @category Fizzy
 * @package Fizzy_Paginator
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
 * @copyright Copyright (c) 2009-2010 Voidwalkers (http://www.voidwalkers.nl)
 * @license http://www.voidwalkers.nl/license/new-bsd The New BSD License
 */

class Fizzy_Paginator_Adapter_DoctrineQuery
    implements Zend_Paginator_Adapter_Interface
{

    protected $_query;
    protected $_count_query;

    public function __construct($query)
    {
        $this->_query = $query;
        $this->_count_query = clone $query;
    }

    public function getItems($offset, $itemsPerPage)
    {
        return $this->_query
            ->limit($itemsPerPage)
            ->offset($offset)
            ->execute();
    }

    public function count()
    {
        return $this->_count_query->count();
    }

}