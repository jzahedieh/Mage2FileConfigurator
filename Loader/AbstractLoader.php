<?php

namespace JZahedieh\FileConfigurator\Loader;

use Magento\Framework\App\Filesystem\DirectoryList;

abstract class AbstractLoader
{
    const XML_ACTIVE = 'dev/jzahedieh_fileconfigurator/active';
    const XML_SYSTEM_PATH = 'dev/jzahedieh_fileconfigurator/system_path';

    const TYPE_NORMAL = 'config';
    const TYPE_ENCRYPTED = 'config_encrypted';

    const BASE_DIR_PLACEHOLDER = '###MAGE_BASE###';

    protected $_types = [self::TYPE_NORMAL, self::TYPE_ENCRYPTED];
    protected $_data = [];
    protected $_errors = [];
    protected $_folderLocation;

    /**
     * @var \Magento\Backend\App\ConfigInterface
     */
    protected $_backendConfig;

    /**
     * @var \Magento\Config\Model\Resource\Config
     */
    protected $_coreConfigResource;

    /**
     * @var \JZahedieh\FileConfigurator\Model\Config
     */
    protected $_configModel;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var \Symfony\Component\Finder\Finder
     */
    protected $_finder;

    /**
     * @var \JZahedieh\FileConfigurator\Model\Resource\Config\Flusher
     */
    protected $_flusher;

    public function __construct(
        \Magento\Backend\App\ConfigInterface $backendConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Config\Model\Resource\Config $coreConfigResource,
        \JZahedieh\FileConfigurator\Model\Config $configModel,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Symfony\Component\Finder\Finder $finder,
        \JZahedieh\FileConfigurator\Model\Resource\Config\Flusher $flusher
    ) {
        $this->_backendConfig = $backendConfig;
        $this->_directory = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->_folderLocation = $this->_getAbsoluteFolderLocation();
        $this->_coreConfigResource = $coreConfigResource;
        $this->_configModel = $configModel;
        $this->_encryptor = $encryptor;
        $this->_finder = $finder;
        $this->_flusher = $flusher;
    }

    /**
     * Process the files from a location
     *
     * @param string $folderLocation
     * @param bool $refreshStorage
     * @return array
     */
    public function process($folderLocation = '', $refreshStorage = true)
    {
        if ($folderLocation) {
            $this->_folderLocation = $folderLocation;
        }

        if ($refreshStorage) {
            $this->_flusher->flush();
        }

        foreach ($this->_types as $type) {
            if (!isset($this->_load()[$type])) {
                continue;
            }

            foreach ($this->_load()[$type] as $entry) {

                foreach ($entry as $path => $value) {
                    $this->_saveConfig($path, $entry['scope_id'], $value, $type);
                }

            }
        }

        return $this->_errors;
    }

    /**
     * Save the configuration value in both core and module db tables.
     *
     * @param $path
     * @param $scopeId
     * @param $value
     * @param string $type
     */
    protected function _saveConfig($path, $scopeId, $value, $type = self::TYPE_NORMAL)
    {
        // do not save config if path validation fails.
        if (!$fullPathParts = $this->_validateFullPath($path)) {
            return;
        }

        if ($type === self::TYPE_ENCRYPTED) {
            $value = $this->_encryptor->encrypt($value);
        }

        // get the path from the parts of path
        $path = implode('/', array_slice($fullPathParts, 1, 3));

        $this->_coreConfigResource->saveConfig($path, $value, $fullPathParts[0], $scopeId);

        $this->_configModel->setData([
            'scope_type' => $fullPathParts[0],
            'scope_id' => $scopeId,
            'path' => $path,
            'value' => $value
        ]);

        $this->_configModel->save();
        $this->_configModel->clearInstance();
    }


    /**
     * Validate the path to make sure has enough parts
     *
     * @param $path
     * @return array|false
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
     * Loads files content
     *
     * @return array
     */
    abstract protected function _load();

    /**
     * Get the list of files in the data folder.
     *
     * @param string $name
     * @return \Symfony\Component\Finder\Finder
     */
    protected function _getConfigFiles($name)
    {
        return $this->_finder->files()
            ->in($this->_folderLocation)
            ->name($name);
    }

    /**
     * Get folder location which holds file configurations
     *
     * @return string
     */
    protected function _getAbsoluteFolderLocation()
    {
        return str_replace(
            self::BASE_DIR_PLACEHOLDER,
            $this->_directory->getAbsolutePath(),
            $this->_backendConfig->getValue(self::XML_SYSTEM_PATH)
        );
    }


}