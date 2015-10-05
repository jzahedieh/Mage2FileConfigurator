<?php

namespace JZahedieh\FileConfigurator\Loader;

use Magento\Framework\App\Filesystem\DirectoryList;

class Yaml
{
    const XML_ACTIVE = 'dev/jzahedieh_fileconfigurator/active';
    const XML_SYSTEM_PATH = 'dev/jzahedieh_fileconfigurator/system_path';

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_backendConfig;

    /**
     * Backend Config Model Factory
     *
     * @var \Magento\Config\Model\Config\Factory
     */
    protected $_configFactory;

    /**
     * @var \Symfony\Component\Yaml\Parser
     */
    protected $_yamlParser;

    protected $_yaml = [];
    protected $_errors = [];
    protected $_folderLocation;

    public function __construct(
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Symfony\Component\Yaml\Parser $yamlParser,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Config\Model\Config\Factory $configFactory
    ) {
        $this->_yamlParser = $yamlParser;
        $this->_backendConfig = $backendConfig;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_folderLocation = str_replace('###MAGE_BASE###', $this->_getBaseDir(), $this->_backendConfig->getValue(self::XML_SYSTEM_PATH));
        $this->_configFactory = $configFactory;
    }


    /**
     * Synchronise the file content with the database.
     *
     * @param string $folderLocation absolute system directory including trailing /
     * @param bool $refreshStorage
     *
     * @return array
     */
    public function process($folderLocation = '', $refreshStorage = true)
    {
        if ($folderLocation) {
            $this->_folderLocation = $folderLocation;
        }

        // clear the storage table as associated config storage
        if ($refreshStorage) {
            //todo
        }

        // load the yaml and process.
        foreach ($this->_load()['config'] as $entry) {
            $scopeId = $entry['scope_id'];
            unset($entry['scope_id']);

            foreach ($entry as $path => $value) {

                // todo: handle encrypted values
                if (is_array($value) && isset($value['encrypted'])) {
                    //$this->_processEncryptedValues($value, $scopeId);
                    continue;
                }

                $this->_saveConfig($path, $scopeId, $value);
            }

        }

        return $this->_errors;
    }

    /**
     * Save config to core_config_data and module storage table.
     *
     * @param $path
     * @param $scopeId
     * @param $value
     *
     */
    protected function _saveConfig($path, $scopeId, $value)
    {
        // do not save config if path validation fails.
        if (!$fullPathParts = $this->_validateFullPath($path)) {
            return;
        }

        // get the path from the parts of path
        $path = implode('/', array_slice($fullPathParts, 1, 3));

        $configData = [
            'section' => 'dev',
            'website' => null,
            'store' => null,
            'groups' => ['jzahedieh_fileconfigurator' => ['fields' => ['test' => 'testval']]]
        ];
        /** @var \Magento\Config\Model\Config $configModel  */
        $configModel = $this->_configFactory->create(['data' => $configData]);
        /**
         * todo: work around session issue, more barebones approach using the resource model needed I think.
         * Area code not set: Area code must be set before starting a session.
         **/
        //$configModel->save();


        //todo save in table

    }

    /**
     * get/validate the path to make sure it is correct.
     *
     * @param $path
     * @return array|bool
     */
    protected function _validateFullPath($path)
    {
        $pathParts = explode('/', $path);

        // check the path is in the correct format
        if (count($pathParts) !== 4) {
            $this->_errors[] = sprintf(
                'path should have 4 nodes, has %d [%s]',
                count($pathParts), $path
            );

            return false;
        }

        return $pathParts;
    }

    /**
     * Loads YAML files content
     *
     * @return array
     */
    protected function _load()
    {
        if (!$this->_yaml) {
            foreach ($this->_getConfigFiles() as $file) {
                //todo: replace with symfony finder
                $yaml = $this->_yamlParser->parse(file_get_contents($file));
                $this->_yaml = array_merge_recursive($this->_yaml, $yaml);
            }
        }

        return $this->_yaml;
    }

    /**
     * Get the list of yaml files in the data folder.
     *
     * @return array
     */
    protected function _getConfigFiles()
    {
        return glob($this->_folderLocation . '*.yaml');
    }

    /**
     * Get base dir
     *
     * @return string
     */
    protected function _getBaseDir()
    {
        return $this->_directory->getAbsolutePath();
    }


}