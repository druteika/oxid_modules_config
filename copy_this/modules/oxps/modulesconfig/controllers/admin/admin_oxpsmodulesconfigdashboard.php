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
 * Class Admin_oxpsModulesConfigDashboard
 * Modules configuration export, backup and import tools controller.
 *
 * @todo: Add a checkbox for import force (ignores shop versions, edition and ID differences)
 * @todo: Add checkbox for ALL sub-shops export / import.
 */
class Admin_oxpsModulesConfigDashboard extends oxAdminView
{

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'admin_oxpsmodulesconfigdashboard.tpl';

    /**
     * Current action name.
     * Supported actions are: export, backup and import.
     *
     * @var string
     */
    protected $_sAction = '';

    /**
     * Success messages to display.
     *
     * @var array
     */
    protected $_aMessages = array();

    /**
     * Data validation instance.
     *
     * @var null|oxpsModulesConfigRequestValidator
     */
    protected $_oValidator = null;


    /**
     * Get a list of all shop modules.
     *
     * @return array
     */
    public function getModulesList()
    {
        /** @var oxpsModulesConfigContent $oContent */
        $oContent = oxRegistry::get('oxpsModulesConfigContent');

        return (array) $oContent->getModulesList();
    }

    /**
     * Get a list of settings available to export and import.
     *
     * @return array
     */
    public function getSettingsList()
    {
        /** @var oxpsModulesConfigContent $oContent */
        $oContent = oxRegistry::get('oxpsModulesConfigContent');

        return (array) $oContent->getSettingsList();
    }

    /**
     * Set current action name.
     *
     * @param string $sAction
     */
    public function setAction($sAction)
    {
        $this->_sAction = $sAction;
    }

    /**
     * Get current action name.
     *
     * @return string
     */
    public function getAction()
    {
        return $this->_sAction;
    }

    /**
     * Add a message to messages list.
     *
     * @param string $sMessageCode
     */
    public function addMessage($sMessageCode)
    {
        $this->_aMessages[] = $sMessageCode;
    }

    /**
     * Get success messages list.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->_aMessages;
    }

    /**
     * Get data validation instance.
     *
     * @return oxpsModulesConfigRequestValidator
     */
    public function getValidator()
    {
        if (is_null($this->_oValidator)) {
            $this->_oValidator = oxRegistry::get('oxpsModulesConfigRequestValidator');
        }

        return $this->_oValidator;
    }

    /**
     * Get data validation errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->getValidator()->getErrors();
    }


    /**
     * Export, backup amd import actions handler.
     * Collect form and import files data, validates it and performs the export or backup, or backup plus import.
     */
    public function actionSubmit()
    {
        $aRequestData = $this->_getRequestData();

        if ($this->getValidator()->validateRequestData($aRequestData)) {
            $this->_invokeAction($this->getAction(), $aRequestData);
        }
    }


    /*------------------------
     *- REQUEST DATA GETTERS -
     ------------------------*/

    /**
     * Collect action data from request parameters.
     * It includes affected modules list, settings list and action name.
     *
     * @return array
     */
    protected function _getRequestData()
    {
        $oConfig = $this->getConfig();

        $sAction = '';
        $aModules = (array) $oConfig->getRequestParameter('oxpsmodulesconfig_modules');
        $aSettings = array_keys((array) $oConfig->getRequestParameter('oxpsmodulesconfig_settings'));

        if ($oConfig->getRequestParameter('oxpsmodulesconfig_export')) {
            $sAction = 'export';
        } elseif ($oConfig->getRequestParameter('oxpsmodulesconfig_backup')) {
            $sAction = 'backup';
        } elseif ($oConfig->getRequestParameter('oxpsmodulesconfig_import')) {
            $sAction = 'import';
        }

        $this->setAction($sAction);

        return array('modules' => $aModules, 'settings' => $aSettings, 'action' => $sAction);
    }

    /**
     * Simulate action data with all modules elected and all settings checked.
     *
     * @return array
     */
    protected function _getAllModulesAndSettingsData()
    {
        return array(
            'modules'  => array_keys($this->getModulesList()),
            'settings' => array_keys($this->getSettingsList()),
            'action'   => ''
        );
    }

    /**
     * Get uploaded import files data.
     *
     * @return null|array
     */
    protected function _getImportData()
    {
        $oConfig = $this->getConfig();

        return $oConfig->getUploadedFile('oxpsmodulesconfig_file');
    }


    /*--------------------
     *----- ACTIONS ------
     --------------------*/

    /**
     * Trigger a proper action by its name.
     *
     * @param string $sAction
     * @param array  $aData
     *
     * @return bool
     */
    protected function _invokeAction($sAction, array $aData)
    {
        $blReturn = true;

        switch ($sAction) {
            case 'export':
                $this->_exportModulesConfig($aData);
                break;

            case 'backup':
                $blReturn = (bool) $this->_backupModuleSettings($aData);
                break;

            case 'import':
                $blReturn = (bool) $this->_importModulesConfig($aData);
                break;
        }

        return $blReturn;
    }

    /**
     * Export checked settings of the selected modules to JSON file and pass it for download.
     *
     * @param array $aData
     */
    protected function _exportModulesConfig(array $aData)
    {
        /** @var oxpsModulesConfigTransfer $oModulesConfig */
        $oModulesConfig = oxNew('oxpsModulesConfigTransfer');
        $oModulesConfig->exportForDownload($aData);

        $this->getValidator()->addError('OXPS_MODULESCONFIG_ERR_EXPORT_FAILED');
    }

    /**
     * Export checked settings of the selected modules to JSON file and save it in file system.
     *
     * @param array  $aData
     * @param string $sBackupFileSuffix
     *
     * @return bool
     */
    protected function _backupModuleSettings(array $aData, $sBackupFileSuffix = '')
    {
        /** @var oxpsModulesConfigTransfer $oModulesConfig */
        $oModulesConfig = oxNew('oxpsModulesConfigTransfer');

        if (!$oModulesConfig->backupToFile($aData, $sBackupFileSuffix)) {
            $this->getValidator()->addError('OXPS_MODULESCONFIG_ERR_BACKUP_FAILED');

            return false;
        } else {
            $this->addMessage('OXPS_MODULESCONFIG_MSG_BACKUP_SUCCESS');

            return true;
        }
    }

    /**
     * Check import data and invoke module configuration data import.
     *
     * @param array $aRequestData
     *
     * @return bool
     */
    protected function _importModulesConfig(array $aRequestData)
    {
        $aImportData = $this->_getImportData();

        if (!$this->getValidator()->validateImportData($aImportData)) {
            return false;
        } else {
            $this->_backupModuleSettings($this->_getAllModulesAndSettingsData(), 'full_backup');
            $this->_runModulesConfigImport($aRequestData, $aImportData);
        }

        return true;
    }

    /**
     * Initialize import helper with uploaded file data and perform import for checked settings of selected modules.
     * On successful import clear eShop cache.
     *
     * @todo: Trigger modules events and update views?
     *
     * @param array $aRequestData
     * @param array $aImportData
     */
    protected function _runModulesConfigImport(array $aRequestData, array $aImportData)
    {
        /** @var oxpsModulesConfigTransfer $oModulesConfig */
        $oModulesConfig = oxNew('oxpsModulesConfigTransfer');
        $oModulesConfig->setImportDataFromFile($aImportData);

        if (!$oModulesConfig->importData($aRequestData)) {
            $this->getValidator()->addErrors((array) $oModulesConfig->getImportErrors());
        } else {
            $this->addMessage('OXPS_MODULESCONFIG_MSG_IMPORT_SUCCESS');

            /** @var oxpsModulesConfigModule $oModule */
            $oModule = oxNew('oxpsModulesConfigModule');
            $oModule->clearTmp();
        }
    }
}
