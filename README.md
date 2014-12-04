# OXID Module Configuration Im-/Exporter

----

Tools to export, backup and import OXID eShop modules configuration data.
It is a GUI based dashboard in eShop administration area under _Service -> Module Configuration Im-/Exporter_.
All relevant modules configuration is being used including: Versions, Extended classes, Module classes, Templates, Blocks, Settings and Events.

## Installation
 - Copy the content of `copy_this/` folder to OXID eShop root folder
 - Activate the module in administration area

## Usage
- Go to _Service -> Module Configuration Im-/Exporter_
- Select which modules and settings to export, backup or import
- Press "Export" to download settings immediately as a JSON file 
- Press "Backup" to save settings in JSON file under `export/modules_config/`
- Choose a file to import and press "Import" to update modules settings from a JSON file
    - Before the import a full backup is done and after the import, eShop cache is cleared

## JSON file structure
- It is built from array containing some general eShop data and modules settings data
- eShop version, edition and sub-shop ID are stored to identify a shop
- Module configuration is split for each module separately by module ID (except "extend" data)
- Module configuration keys are same as in metadata file and value are same as stored in eShop and non encrypted
- Since it is a text file, it could be also edited by hand and put under version control!
- Note, that import JSON files encoding should be UTF-8 without BOM

## To do and nice to have features for future releases
- Force mode to allow importing configuration to any eShop without checking versions
- Action to delete modules settings from database
- An option to export and import off all sub-shops data in one file
- Automatic restore of last backup on at least one setting import failure
- For extended classes settings also split it by modules ID (metadata parsing needed)
- Add an option to export / import global CMS snippets used by modules
- On a new module (not installed module) data import, trigger activation event and rebuild views
- Log import process to a file
- More validation rules for import data: check if imported and if selected modules match import file
