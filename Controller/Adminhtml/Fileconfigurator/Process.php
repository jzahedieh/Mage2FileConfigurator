<?php
namespace JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator;

class Process extends \JZahedieh\FileConfigurator\Controller\Adminhtml\Fileconfigurator
{
    /**
     * @return void
     */
    public function execute()
    {
        try {
            $this->_objectManager->create(
                'JZahedieh\FileConfigurator\Loader\YamlLoader'
            )->process();

            $this->messageManager->addSuccess(__('The file configurator been processed.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __("We couldn't process the file configurator because of an error.")
            );
        }

        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
        return;
    }
}
