<?php
/**
 * This file is part of OXID Module Configuration Im-/Exporter module.
 *
 * OXID Module Configuration Im-/Exporter module is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * OXID Module Configuration Im-/Exporter module is distributed in
 * the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License
 * for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Module Configuration Im-/Exporter module.
 * If not, see <http://www.gnu.org/licenses/>.
 *
 * @category      module
 * @package       modulesconfig
 * @author        OXID Professional services
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Class oxpsModulesConfigTransfer
 * Modules configuration export, backup and import actions handler.
 *
 * @todo: Collect JSON encode/decode errors from json_last_error()?
 */
class oxpsModulesConfigTransfer extends oxSuperCfg
{

    /**
     * Import data.
     *
     * @var array
     */
    protected $_aImportData = array();


    /**
     * Set import data array.
     *
     * @param array $aImportData
     */
    public function setImportData(array $aImportData)
    {
        $this->_aImportData = $aImportData;
    }

    /**
     * Get import data array.
     *
     * @return array
     */
    public function getImportData()
    {
        return $this->_aImportData;
    }

    /**
     * Collect requested settings for selected modules, build JSON export file and pass it for download.
     *
     * @param array $aExportParameters
     */
    public function exportForDownload(array $aExportParameters)
    {
        $aExportData = (array) $this->_getSettingsData($aExportParameters);
        $sFileName = $this->_getJsonFileName();

        $this->_jsonDownload($sFileName, $aExportData);
    }

    /**
     * Collect requested settings for selected modules, build JSON backup file and save it in file system.
     *
     * @param array  $aBackupParameters
     * @param string $sBackupFileSuffix
     *
     * @return int
     */
    public function backupToFile(array $aBackupParameters, $sBackupFileSuffix = 'manual_backup')
    {
        $aBackupData = (array) $this->_getSettingsData($aBackupParameters);
        $sBackupsPath = $this->_getBackupFolderPath();
        $sFileName = $this->_getJsonFileName($sBackupFileSuffix);

        return $this->_jsonBackup($sBackupsPath . $sFileName, $aBackupData);
    }

    /**
     * Get import data from uploaded file and set it decoded from JSON to array.
     *
     * @codeCoverageIgnore
     *
     * @param array $aImportFileData
     */
    public function setImportDataFromFile(array $aImportFileData)
    {
        if (!empty($aImportFileData['tmp_name']) and is_file($aImportFileData['tmp_name'])) {
            $sData = file_get_contents($aImportFileData['tmp_name']);

            if (!empty($sData)) {
                $this->setImportData($this->_json_decode($sData));
            }
        }
    }

    /**
     * Validate import data and return errors list if any.
     *
     * @return array
     */
    public function getImportDataValidationErrors()
    {
        /** @var oxpsModulesConfigJsonValidator $oImportDataValidator */
        $oImportDataValidator = oxRegistry::get('oxpsModulesConfigJsonValidator');
        $oImportDataValidator->init($this->getImportData(), $this->_getSettingsDataHeader());

        return (array) $oImportDataValidator->validateJsonData();
    }

    /**
     * Import modules configuration data for checked settings of selected modules.
     *
     * @todo: Logging to file (use OXPS Logger?).
     * @todo: Roll back to last automatic full backup on failure.
     *
     * @param array $aParameters
     *
     * @return bool
     */
    public function importData(array $aParameters)
    {
        $aAllImportData = $this->getImportData();
        $aImportData = (array) reset($aAllImportData);

        if (!isset($aImportData['aModules'], $aParameters['modules'], $aParameters['settings']) or
            !is_array($aParameters['modules']) or
            !is_array($aParameters['settings'])
        ) {
            return false;
        }

        return $this->_setSettingsValues(
            (array) $aImportData['aModules'],
            $aParameters['modules'],
            $aParameters['settings']
        );
    }

    /**
     * Get errors that occurred during import.
     *
     * @todo: Implement it when Logging is implemented (should have both success log and import errors)
     *
     * @return array
     */
    public function getImportErrors()
    {
        return array();
    }

    /**
     * Collect requested settings for selected modules for data export.
     *
     * @param array $aParameters
     *
     * @return array
     */
    protected function _getSettingsData(array $aParameters)
    {
        $aModules = array();

        if (isset($aParameters['modules'], $aParameters['settings']) and
            is_array($aParameters['modules']) and
            is_array($aParameters['settings'])
        ) {
            $aModules = $this->_getSettingsValues($aParameters['modules'], $aParameters['settings']);
        }

        return $this->_getSettingsDataHeader($aModules);
    }

    /**
     * Get requested settings list for each requested module.
     *
     * @param array $aRequestedModules
     * @param array $sRequestedSettings
     *
     * @return array
     */
    protected function _getSettingsValues(array $aRequestedModules, array $sRequestedSettings)
    {
        $aModules = array();

        foreach ($aRequestedModules as $sModuleId) {
            if (!array_key_exists($sModuleId, $aModules)) {
                $aModules[$sModuleId] = array();
            }

            foreach ($sRequestedSettings as $sSetting) {
                $aModules[$sModuleId][$sSetting] = $this->_getSettingValue($sModuleId, $sSetting);
            }
        }

        return $aModules;
    }

    /**
     * Set requested settings list for each requested module from provided import data.
     *
     * @param array $aImportModules
     * @param array $aRequestedModules
     * @param array $aRequestedSettings
     *
     * @return bool True if at least one setting was updated, False otherwise.
     */
    protected function _setSettingsValues(array $aImportModules, array $aRequestedModules, array $aRequestedSettings)
    {
        $blSettingUpdated = false;

        foreach ($aRequestedModules as $sModuleId) {
            if (array_key_exists($sModuleId, $aImportModules)) {
                $aImportModule = (array) $aImportModules[$sModuleId];
                $blModuleUpdated = $this->_setModuleSettingsValues($sModuleId, $aImportModule, $aRequestedSettings);
                $blSettingUpdated = ($blSettingUpdated or $blModuleUpdated);
            }
        }

        return $blSettingUpdated;
    }

    /**
     * Set requested settings list for a module.
     *
     * @param string $sModuleId
     * @param array  $aModuleData
     * @param array  $aRequestedSettings
     *
     * @return bool
     */
    protected function _setModuleSettingsValues($sModuleId, array $aModuleData, array $aRequestedSettings)
    {
        $blSettingUpdated = false;

        foreach ($aRequestedSettings as $sSetting) {
            if (array_key_exists($sSetting, $aModuleData)) {
                $this->_setSettingValue($sModuleId, $sSetting, $aModuleData[$sSetting]);
                $blSettingUpdated = true;
            }
        }

        return $blSettingUpdated;
    }

    /**
     * Create a name for JSON export or backup file.
     *
     * @param string $sBackupSuffix Additional prefix for backup file name
     *
     * @return string
     */
    protected function _getJsonFileName($sBackupSuffix = '')
    {
        return sprintf(
            'oxid_modules_config_%s%s.json',
            date('Y-m-d_H-i-s'),
            empty($sBackupSuffix) ? '' : ('.' . $sBackupSuffix)
        );
    }

    /**
     * Fetch JSON data as a file download.
     *
     * @codeCoverageIgnore
     *
     * @param string $sFileName
     * @param array  $aFileData
     */
    protected function _jsonDownload($sFileName, array $aFileData)
    {
        ob_clean();

        header('Content-disposition: attachment; filename=' . $sFileName);
        header('Content-Type: application/json; charset=UTF-8');

        exit($this->_json_encode($aFileData));
    }

    /**
     * Get full path to eShop export dir to store modules configuration backups in.
     * If modules configuration backups folder is missing,
     * it creates the folder and .htaccess file to forbid direct web access.
     *
     * @return string
     */
    protected function _getBackupFolderPath()
    {
        /** @var oxConfig $oConfig */
        $oConfig = $this->getConfig();

        $sShopDirPath = (string) $oConfig->getConfigParam('sShopDir');
        $sBackupDirPath = $sShopDirPath . 'export' . DIRECTORY_SEPARATOR . 'modules_config' . DIRECTORY_SEPARATOR;

        $this->_touchBackupsDir($sBackupDirPath);

        return $sBackupDirPath;
    }

    /**
     * Save data in JSON format to a backup file.
     *
     * @codeCoverageIgnore
     *
     * @param string $sFullFilePath
     * @param array  $aFileData
     *
     * @return int
     */
    protected function _jsonBackup($sFullFilePath, array $aFileData)
    {
        return file_put_contents($sFullFilePath, $this->_json_encode($aFileData));
    }

    /**
     * Check if folder exists, if not create it and put .htaccess file there to forbid web access.
     *
     * @codeCoverageIgnore
     *
     * @param string $sFolderPath
     */
    protected function _touchBackupsDir($sFolderPath)
    {
        if (!is_dir($sFolderPath)) {
            mkdir($sFolderPath, 0777);
            file_put_contents($sFolderPath . '.htaccess', 'deny from all' . PHP_EOL);
        }
    }

    /**
     * Get an array with basic shop data for modules data to set in.
     * Put modules data array argument inside it.
     *
     * @param array $aModules
     *
     * @return array
     */
    protected function _getSettingsDataHeader(array $aModules = array())
    {
        /** @var oxConfig $oConfig */
        $oConfig = $this->getConfig();

        return array(
            '_OXID_ESHOP_MODULES_CONFIGURATION_' => array(
                'sShopVersion' => $oConfig->getVersion(),
                'sShopEdition' => $oConfig->getEdition(),
                'sShopId'      => $oConfig->getShopId(),
                'aModules'     => $aModules,
            )
        );
    }

    /**
     * Get setting values by setting name.
     * Maps setting name to its related class and key or field.
     *
     * @param string $sModuleId
     * @param string $sSetting
     *
     * @return mixed
     */
    protected function _getSettingValue($sModuleId, $sSetting)
    {
        /** @var oxpsModulesConfigStorage $oConfigurationStorage */
        $oConfigurationStorage = oxRegistry::get('oxpsModulesConfigStorage');

        return $oConfigurationStorage->load($sModuleId, $sSetting);
    }

    /**
     * Set module setting value.
     * Maps setting name to its related class and key or field and saves the value.
     *
     * @param string $sModuleId
     * @param string $sSetting
     * @param mixed  $mValue
     */
    protected function _setSettingValue($sModuleId, $sSetting, $mValue)
    {
        /** @var oxpsModulesConfigStorage $oConfigurationStorage */
        $oConfigurationStorage = oxRegistry::get('oxpsModulesConfigStorage');

        $oConfigurationStorage->save($sModuleId, $sSetting, $mValue);
    }

    /**
     * Encode array in JSON format.
     * If it's available, pretty printing option is used to make file more readable.
     *
     * @codeCoverageIgnore
     *
     * @param array $aData
     *
     * @return string
     */
    protected function _json_encode(array $aData)
    {
        if (defined('JSON_PRETTY_PRINT')) {
            $sData = json_encode($aData, JSON_PRETTY_PRINT);
        } else {
            $sData = json_encode($aData);
        }

        return $sData;
    }

    /**
     * Decode JSON string data to an assoc array.
     *
     * @codeCoverageIgnore
     *
     * @param string $sData
     *
     * @return array
     */
    protected function _json_decode($sData)
    {
        return (array) json_decode((string) $sData);
    }
}
