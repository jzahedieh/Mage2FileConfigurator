<?php
namespace Jzahedieh\FileConfigurator\Controller\Adminhtml;

class Fileconfigurator extends \Magento\Backend\App\AbstractAction
{
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        $result = $this->_authorization->isAllowed('JZahedieh_FileConfigurator::jzahedieh_fileconfigurator');
        return $result;
    }
}
