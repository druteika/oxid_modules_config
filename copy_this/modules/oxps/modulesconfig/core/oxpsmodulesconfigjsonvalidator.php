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
 * Class oxpsModulesConfigJsonValidator
 * Modules configuration import JSON data validation helper.
 */
class oxpsModulesConfigJsonValidator extends oxSuperCfg
{

    /**
     * Import data to validate.
     *
     * @var array
     */
    protected $_aImportData = array();

    /**
     * Array structure for modules configuration transfer.
     * Used to validate import data structure against it.
     *
     * @var array
     */
    protected $_aSettingsDataHeader = array();


    /**
     * Initialize validation helper for JSON data verification.
     *
     * @param array $aImportData
     * @param array $aSettingsDataHeader
     */
    public function init(array $aImportData, array $aSettingsDataHeader)
    {
        $this->_aImportData = $aImportData;
        $this->_aSettingsDataHeader = $aSettingsDataHeader;
    }

    /**
     * Validate JSON data to be not empty, to have a proper format and match current shop.
     *
     * @return array
     */
    public function validateJsonData()
    {
        $aValidationMethods = array(
            '_validateImportDataNotEmpty',
            '_validateImportDataFormat',
            '_validateImportDataMatchesShop',
            '_validateImportDataSettingsValid',
        );

        foreach ($aValidationMethods as $sValidationMethod) {
            $aErrors = (array) $this->$sValidationMethod();

            if (!empty($aErrors)) {
                return $aErrors;
            }
        }

        return array();
    }


    /**
     * Validate import data to be set.
     *
     * @return array
     */
    protected function _validateImportDataNotEmpty()
    {
        if (empty($this->_aImportData)) {
            return array('OXPS_MODULESCONFIG_ERR_EMPTY_DATA');
        }

        return array();
    }

    /**
     * Validate import data to have proper format - match settings data header array keys.
     *
     * @return array
     */
    protected function _validateImportDataFormat()
    {
        if (array_keys($this->_aSettingsDataHeader) !== array_keys($this->_aImportData) or
            array_keys(reset($this->_aSettingsDataHeader)) !== array_keys((array) reset($this->_aImportData))
        ) {
            return array('OXPS_MODULESCONFIG_ERR_INVALID_FORMAT');
        }

        return array();
    }

    /**
     * Validate import data to march shop version, edition and sub-shop ID.
     *
     * @todo: Created force mode (checkbox in template) to skip this validation and import data to any shop.
     *
     * @return array
     */
    protected function _validateImportDataMatchesShop()
    {
        $aErrors = array();

        $aCurrentShopData = reset($this->_aSettingsDataHeader);
        $aImportFileData = (array) reset($this->_aImportData);

        if ($aCurrentShopData['sShopVersion'] !== $aImportFileData['sShopVersion']) {
            $aErrors[] = 'OXPS_MODULESCONFIG_ERR_SHOP_VERSION';
        }

        if ($aCurrentShopData['sShopEdition'] !== $aImportFileData['sShopEdition']) {
            $aErrors[] = 'OXPS_MODULESCONFIG_ERR_SHOP_EDITION';
        }

        if ($aCurrentShopData['sShopId'] !== $aImportFileData['sShopId']) {
            $aErrors[] = 'OXPS_MODULESCONFIG_ERR_WRONG_SUBSHOP';
        }

        return $aErrors;
    }

    /**
     * Validate import data to contain proper modules configuration data.
     *
     * @todo: Add more validation rules to check import data itself.
     * @todo: Maybe also validate it against request data to check if modules and settings match.
     *
     * @return array
     */
    protected function _validateImportDataSettingsValid()
    {
        return array();
    }
}
