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
 * Class oxpsModulesConfigRequestValidator
 * Modules configuration data validation and error handler.
 */
class oxpsModulesConfigRequestValidator extends oxSuperCfg
{

    /**
     * Validation errors.
     *
     * @var array
     */
    protected $_aErrors = array();


    /**
     * Reset errors list to empty array.
     */
    public function resetError()
    {
        $this->_aErrors = array();
    }

    /**
     * Add an error to errors list.
     *
     * @param string $sErrorCode
     */
    public function addError($sErrorCode)
    {
        $this->_aErrors[] = $sErrorCode;
    }

    /**
     * Add multiple errors to the list.
     *
     * @param array $aErrors
     */
    public function addErrors(array $aErrors)
    {
        $this->_aErrors = array_merge($this->_aErrors, $aErrors);
    }

    /**
     * Get detected errors list.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_aErrors;
    }


    /**
     * Validate request data and collect errors if any.
     *
     * @param array $aData
     *
     * @return bool
     */
    public function validateRequestData(array $aData)
    {
        $this->resetError();

        $this->_validateModulesData($aData);
        $this->_validateSettingsData($aData);
        $this->_validateActionData($aData);

        return !$this->getErrors();
    }

    /**
     * Validate uploaded import file data and content and collect errors if any.
     *
     * @param array $aData
     *
     * @return bool
     */
    public function validateImportData(array $aData)
    {
        if (empty($aData)) {
            $this->addError('OXPS_MODULESCONFIG_ERR_NO_FILE');

            return false;
        }

        if (!empty($aData['error'])) {
            $this->_setFileUploadError($aData['error']);

            return false;
        }

        $this->_validateImportFile($aData);
        $this->_validateJsonData($aData);

        return !$this->getErrors();
    }


    /**
     * Modules data should not be empty and each module ID should be available.
     *
     * @param array $aData
     */
    protected function _validateModulesData(array $aData)
    {
        if (empty($aData['modules']) or !is_array($aData['modules'])) {
            $this->addError('OXPS_MODULESCONFIG_ERR_NO_MODULES');

            return;
        }

        $this->_checkIfModulesAreAvailable($aData['modules']);
    }

    /**
     * Check if modules are available.
     *
     * @param array $aModules
     */
    protected function _checkIfModulesAreAvailable(array $aModules)
    {
        /** @var oxpsModulesConfigContent $oContent */
        $oContent = oxRegistry::get('oxpsModulesConfigContent');
        $aValidModules = $oContent->getModulesList();

        foreach ($aModules as $sModule) {
            if (!array_key_exists($sModule, $aValidModules)) {
                $this->addError('OXPS_MODULESCONFIG_ERR_INVALID_MODULE');
                break;
            }
        }
    }

    /**
     * Settings data should not be empty and each setting name should be available.
     *
     * @param array $aData
     */
    protected function _validateSettingsData(array $aData)
    {
        if (empty($aData['settings']) or !is_array($aData['settings'])) {
            $this->addError('OXPS_MODULESCONFIG_ERR_NO_SETTINGS');

            return;
        }

        $this->_checkIfSettingsAreAvailable($aData['settings']);
    }

    /**
     * Check if settings are available.
     *
     * @param array $aSettings
     */
    protected function _checkIfSettingsAreAvailable(array $aSettings)
    {
        /** @var oxpsModulesConfigContent $oContent */
        $oContent = oxRegistry::get('oxpsModulesConfigContent');
        $aValidSettings = $oContent->getSettingsList();

        foreach ($aSettings as $sSettings) {
            if (!array_key_exists($sSettings, $aValidSettings)) {
                $this->addError('OXPS_MODULESCONFIG_ERR_INVALID_SETTING');
                break;
            }
        }
    }

    /**
     * Action name should be not empty and among available actions.
     *
     * @param array $aData
     */
    protected function _validateActionData(array $aData)
    {
        if (empty($aData['action']) or !in_array($aData['action'], array('export', 'backup', 'import'))) {
            $this->addError('OXPS_MODULESCONFIG_ERR_INVALID_ACTION');
        }
    }

    /**
     * Set error by file upload error code.
     *
     * @param int $iFileUploadErrorCode
     */
    protected function _setFileUploadError($iFileUploadErrorCode)
    {
        $aFileUploadErrors = array(
            UPLOAD_ERR_INI_SIZE   => 'OXPS_MODULESCONFIG_ERR_FILE_SIZE',
            UPLOAD_ERR_FORM_SIZE  => 'OXPS_MODULESCONFIG_ERR_FILE_SIZE',
            UPLOAD_ERR_PARTIAL    => 'OXPS_MODULESCONFIG_ERR_UPLOAD_ERROR',
            UPLOAD_ERR_NO_FILE    => 'OXPS_MODULESCONFIG_ERR_NO_FILE',
            UPLOAD_ERR_NO_TMP_DIR => 'OXPS_MODULESCONFIG_ERR_UPLOAD_ERROR',
            UPLOAD_ERR_CANT_WRITE => 'OXPS_MODULESCONFIG_ERR_UPLOAD_ERROR',
            UPLOAD_ERR_EXTENSION  => 'OXPS_MODULESCONFIG_ERR_FILE_TYPE',
        );

        $sErrorCode = array_key_exists($iFileUploadErrorCode, $aFileUploadErrors)
            ? $aFileUploadErrors[$iFileUploadErrorCode]
            : 'OXPS_MODULESCONFIG_ERR_UPLOAD_ERROR';

        $this->addError($sErrorCode);
    }

    /**
     * Validate uploaded file to be of JSON type and readable.
     *
     * @param array $aFileData
     */
    protected function _validateImportFile(array $aFileData)
    {
        if (!$this->_checkIfTypeIsValid($aFileData)) {
            $this->addError('OXPS_MODULESCONFIG_ERR_FILE_TYPE');
        } elseif (!$this->_checkIfFileIsReadable($aFileData)) {
            $this->addError('OXPS_MODULESCONFIG_ERR_CANNOT_READ');
        }
    }

    /**
     * Check if file type is not empty and is JSON.
     *
     * @param array $aFileData
     *
     * @return bool
     */
    protected function _checkIfTypeIsValid(array $aFileData)
    {
        return (
            !empty($aFileData['type']) and
            in_array($aFileData['type'], array('application/json', 'application/octet-stream'))
        );
    }

    /**
     * Check if temp file path is not empty and the file is readable.
     *
     * @param array $aFileData
     *
     * @return bool
     */
    protected function _checkIfFileIsReadable(array $aFileData)
    {
        return (!empty($aFileData['tmp_name']) and $this->_isReadableFile($aFileData['tmp_name']));
    }

    /**
     * Check if there is a readable file under a path.
     *
     * @codeCoverageIgnore
     *
     * @param string $aFilePath
     *
     * @return bool
     */
    protected function _isReadableFile($aFilePath)
    {
        return (is_file($aFilePath) and is_readable($aFilePath));
    }

    /**
     * Set JSON import data from file and check modules configuration data for errors.
     * Checks it only if there are no other errors.
     *
     * @param array $aFileData
     */
    protected function _validateJsonData(array $aFileData)
    {
        if (!$this->getErrors()) {

            /** @var oxpsModulesConfigTransfer $oModulesConfig */
            $oModulesConfig = oxNew('oxpsModulesConfigTransfer');
            $oModulesConfig->setImportDataFromFile($aFileData);

            $this->addErrors((array) $oModulesConfig->getImportDataValidationErrors());
        }
    }
}
