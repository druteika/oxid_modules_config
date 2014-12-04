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

$sLangName = 'Deutsch';

$aLang = array(
    'charset'                                => 'ISO-8859-15', // Supports DE chars like: ä, ü, ö, etc.
    'oxpsmodulesconfig'                      => 'Modulkonfigurations-Im-/Exporter',

    // Common dashboard translations
    'OXPS_MODULESCONFIG_DASHBOARD'           => 'Modulkonfigurations-Export und -Import',
    'OXPS_MODULESCONFIG_NO_MODULES'          => 'Keine Module für Konfigurationsexport oder -import verfügbar',

    // Form translations
    'OXPS_MODULESCONFIG_MODULES'             => 'Module für Export oder Import auswählen.',
    'OXPS_MODULESCONFIG_MODULES_HELP'        => 'Halten Sie "Strg" gedrückt und wählen Sie dann Module, die von der Aktion ' .
                                                'zum Export oder Import der Konfigurationsdaten betroffen sein sollen.',
    'OXPS_MODULESCONFIG_ALL'                 => 'Alle auswählen',
    'OXPS_MODULESCONFIG_NONE'                => 'Alle abwählen',
    'OXPS_MODULESCONFIG_SETTINGS'            => 'Einstellung für Export oder import wählen',
    'OXPS_MODULESCONFIG_SETTINGS_HELP'       => 'Bitte wählen Sie Einstellungs-Typen ab, die von der Aktion zum Export oder ' .
                                                'Import der Konfiguration nicht betoffen sein sollen.',
    'OXPS_MODULESCONFIG_EXPORT'              => 'Export',
    'OXPS_MODULESCONFIG_EXPORT_HELP'         => 'Alle ausgewählten Einstellungen der ausgewählten Module werden im JSON-Format ' .
                                                'exportiert und zum Download angeboten.',
    'OXPS_MODULESCONFIG_BACKUP'              => 'Sicherung',
    'OXPS_MODULESCONFIG_BACKUP_HELP'         => 'Alle ausgewählten Einstellungen der ausgewählten Module werden im JSON-Format ' .
                                                'exportiert und im Dateisystem unter "export/modules_config/" abgelegt.',
    'OXPS_MODULESCONFIG_FILE'                => 'Wählen Sie eine JSON-Datei für den Import',
    'OXPS_MODULESCONFIG_FILE_HELP'           => 'Diese Datei muss ein gültiges JSON-Format mit OXID Modulkonfigurationsdaten enthalten.',
    'OXPS_MODULESCONFIG_IMPORT'              => 'Import',
    'OXPS_MODULESCONFIG_IMPORT_HELP'         => 'Alle ausgewählten Einstellungen der ausgewählten Module werden durch die ' .
                                                'entsprechenden Werte aus der importierten JSON-Datei überschrieben. ' .
                                                'Ein automatisches Backup wird vor dem Import angelegt.',

    // Module settings translations
    'OXPS_MODULESCONFIG_SETTING_VERSION'     => 'Versionen',
    'OXPS_MODULESCONFIG_SETTING_EXTEND'      => 'Erweiterte Klassen',
    'OXPS_MODULESCONFIG_SETTING_FILES'       => 'Moduldateien',
    'OXPS_MODULESCONFIG_SETTING_TEMPLATES'   => 'Templates',
    'OXPS_MODULESCONFIG_SETTING_BLOCKS'      => 'Blöcke',
    'OXPS_MODULESCONFIG_SETTING_SETTINGS'    => 'Einstellungen',
    'OXPS_MODULESCONFIG_SETTING_EVENTS'      => 'Events',

    // Errors
    'OXPS_MODULESCONFIG_ERR_NO_MODULES'      => 'Bitte wählen Sie mindestens ein Modul.',
    'OXPS_MODULESCONFIG_ERR_INVALID_MODULE'  => 'Ungültiges Modul ausgewählt! Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.',
    'OXPS_MODULESCONFIG_ERR_NO_SETTINGS'     => 'Bitte wählen Sie mindestens eine Art von Einstellungen aus.',
    'OXPS_MODULESCONFIG_ERR_INVALID_SETTING' => 'Ungültige Einstellung ausgewählt! Bitte laden Sie die Seite neu und versuchen Sie es noch einmal.',
    'OXPS_MODULESCONFIG_ERR_INVALID_ACTION'  => 'Ungültige Anfrage. Bitte versuchen Sie es noch einmal.',
    'OXPS_MODULESCONFIG_ERR_EXPORT_FAILED'   => 'Export fehlgeschlagen. Bitte versuchen Sie es später noch einmal.',
    'OXPS_MODULESCONFIG_ERR_BACKUP_FAILED'   => 'Sicherung fehlgeschlagen. ' .
                                                'Bitte stellen Sie sicher, dass der eShop-Ordner "export/" beschreibbar ist und versuchen Sie es noch einmal.',

    // File upload errors
    'OXPS_MODULESCONFIG_ERR_NO_FILE'         => 'Bitte wählen Sie eine gültige Datei im JSON-Format für den Konfigurationsimport.',
    'OXPS_MODULESCONFIG_ERR_FILE_SIZE'       => 'Die hochgeladene Datei ist zu groß. Bitte wenden Sie sich an Ihren Systemadministrator.',
    'OXPS_MODULESCONFIG_ERR_UPLOAD_ERROR'    => 'Upload fehlgeschlagen. Bitte versuchen Sie es später noch einmal.',
    'OXPS_MODULESCONFIG_ERR_FILE_TYPE'       => 'Dateiformat ungültig. Bitte wählen Sie eine Datei in JSON-Format, ' .
                                                'die Modulkonfigurationsdaten enthält.',
    'OXPS_MODULESCONFIG_ERR_CANNOT_READ'     => 'Die Datei konnte nicht gelesen werden. Bitte wählen Sie eine Datei in JSON-Format, ' .
                                                'die Modulkonfigurationsdaten enthält.',

    // JSON data errors
    'OXPS_MODULESCONFIG_ERR_EMPTY_DATA'      => 'Import-Daten nicht gefunden. Bitte wählen Sie eine Datei im JSON-Format, ' .
                                                'die Modulkonfigurationsdaten enthält.',
    'OXPS_MODULESCONFIG_ERR_INVALID_FORMAT'  => 'Datenformat der Import-Datei ungültig. Bitte wählen Sie eine Datei im JSON-Format, ' .
                                                'die Modulkonfigurationsdaten enthält.',
    'OXPS_MODULESCONFIG_ERR_SHOP_VERSION'    => 'Importdaten abgewiesen, da die Shop-Version nicht übereinstimmt.',
    'OXPS_MODULESCONFIG_ERR_SHOP_EDITION'    => 'Importdaten abgewiesen, da die Shop-Edition nicht übereinstimmt.',
    'OXPS_MODULESCONFIG_ERR_WRONG_SUBSHOP'   => 'Importdaten abgewiesen, da die Subshop-ID nicht übereinstimmt.',

    // Messages
    'OXPS_MODULESCONFIG_MSG_BACKUP_SUCCESS'  => 'Sicherung erfolgreich angelegt. ' .
                                                'Die Datei wurde im eShop-Ordner unter "export/modules_config/" abgelegt.',
    'OXPS_MODULESCONFIG_MSG_IMPORT_SUCCESS'  => 'Modulkonfiguration erfolgreich importiert.',
);
