<?php
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