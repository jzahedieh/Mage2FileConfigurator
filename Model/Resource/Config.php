<?php
namespace JZahedieh\FileConfigurator\Model\Resource;


class Config extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('jzahedieh_fileconfigurator', 'id');
    }


}
