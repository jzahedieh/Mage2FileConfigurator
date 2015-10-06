<?php

namespace JZahedieh\FileConfigurator\Model\Resource\Config;

/**
 * todo: not quite sure this is the correct way to decouple functionality like this, check if should on resource level
 */
class Flusher
{

    /**
     * @var \Magento\Config\Model\Resource\Config
     */
    protected $_coreConfigResource;

    /**
     * @var \Magento\Config\Model\Resource\Config
     */
    protected $_collection;

    public function __construct(
        \Magento\Config\Model\Resource\Config $coreConfigResource,
        \JZahedieh\FileConfigurator\Model\Resource\Config\Collection $collection
    ) {
        $this->_coreConfigResource = $coreConfigResource;
        $this->_collection = $collection;
    }

    /**
     * Flush the saved configuration from by core and module tables.
     */
    public function flush()
    {
        foreach ($this->_collection->load() as $entry) {
            $this->_coreConfigResource->deleteConfig($entry->getPath(), $entry->getScopeType(), $entry->getScopeId());
        }

        $this->_collection->walk('delete');
    }

}
