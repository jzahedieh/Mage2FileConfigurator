<?php
namespace JZahedieh\FileConfigurator\Model;

class Config extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('JZahedieh\FileConfigurator\Model\Resource\Config');
    }


}
