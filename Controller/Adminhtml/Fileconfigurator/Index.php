<?php
namespace JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator;

class Index extends \JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu(
            'JZahedieh_FileConfigurator::jzahedieh_fileconfigurator'
        )->_addBreadcrumb(
            __('Configuration'),
            __('List')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('File Configurator'));
        $this->_view->renderLayout();
    }
}
