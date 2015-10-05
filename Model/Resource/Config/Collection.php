<?php

namespace JZahedieh\FileConfigurator\Model\Resource\Config;

class Collection extends \Magento\Framework\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource collection initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('JZahedieh\FileConfigurator\Model\Config', 'JZahedieh\FileConfigurator\Model\Resource\Config');
    }

}
