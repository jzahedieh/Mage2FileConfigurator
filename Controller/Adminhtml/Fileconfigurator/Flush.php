<?php
namespace JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator;

class Flush extends \JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator
{
    /**
     * @return void
     */
    public function execute()
    {
        try {
            $this->_objectManager->create(
                'JZahedieh\FileConfigurator\Model\Resource\Config\Flusher'
            )->flush();

            $this->messageManager->addSuccess(__('The file configurator and core config data has been flushed.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __("We couldn't flush the file configurator because of an error.")
            );
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
        return;

    }
}
