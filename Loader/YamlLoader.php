<?php

namespace JZahedieh\FileConfigurator\Loader;

class YamlLoader extends \JZahedieh\FileConfigurator\Loader\AbstractLoader
{

    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $_yamlParser;

    public function __construct(
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Config\Model\Resource\Config $coreConfigResource,
        \JZahedieh\FileConfigurator\Model\Config $configModel,
        \JZahedieh\FileConfigurator\Model\Resource\Config\Collection $configCollection,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Symfony\Component\Finder\Finder $finder,
        \JZahedieh\FileConfigurator\Model\Resource\Config\Flusher $flusher,
        \Symfony\Component\Yaml\Parser $yamlParser
    ) {
        parent::__construct(
            $backendConfig, $filesystem, $coreConfigResource,
            $configModel, $encryptor, $finder, $flusher
        );

        $this->_yamlParser = $yamlParser;
    }

    /**
     * Loads YAML files content for processing.
     *
     * @return array
     */
    protected function _load()
    {
        if (!$this->_data) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            foreach ($this->_getConfigFiles("*.yaml") as $file) {
                $yaml = $this->_yamlParser->parse($file->getContents());
                $this->_data = array_merge_recursive($this->_data, $yaml);
            }
        }

        return $this->_data;
    }

}