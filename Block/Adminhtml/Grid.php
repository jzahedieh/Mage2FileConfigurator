<?php

namespace JZahedieh\FileConfigurator\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'JZahedieh_FileConfigurator';
        $this->_headerText = __('File Configurator');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->_addFlushButton();
        $this->_addProcessButton();
    }

    /**
     * Process button that redirects to action
     *
     * @return $this
     */
    protected function _addProcessButton()
    {
        $this->addButton(
            'process',
            [
                'label' => __('Process'),
                'onclick' => 'setLocation(\'' . $this->getProcessUrl() . '\')',
                'class' => 'add'
            ]
        );
    }

    /**
     * Flush button with delete confirmation popup
     *
     * @return $this
     */
    protected function _addFlushButton()
    {
        $confirmMessage = __(
            'Are you sure you want to remove all configuration information in the grid from the Magento configuration?'
        );


        $this->addButton(
            'flush',
            [
                'label' => __('Flush'),
                'onclick' => 'deleteConfirm(\'' . $confirmMessage . '\', \'' . $this->getFlushUrl() . '\')',
                'class' => 'delete'
            ]
        );
    }

    /**
     * Process action route
     *
     * @return string
     */
    public function getProcessUrl()
    {
        return $this->getUrl('*/*/process');
    }

    /**
     * Flush action route
     *
     * @return string
     */
    public function getFlushUrl()
    {
        return $this->getUrl('*/*/flush');
    }
}
