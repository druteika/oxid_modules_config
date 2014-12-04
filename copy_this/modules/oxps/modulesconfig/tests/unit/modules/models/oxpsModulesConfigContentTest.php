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
 * Class oxpsModulesConfigContentTest
 * Tests for model oxpsModulesConfigContent.
 *
 * @see oxpsModulesConfigContent
 */
class oxpsModulesConfigContentTest extends OxidTestCase
{

    /**
     * Subject under the test.
     *
     * @var oxpsModulesConfigContent
     */
    protected $SUT;


    /**
     * Set SUT state before test.
     */
    public function setUp()
    {
        parent::setUp();

        $this->SUT = $this->getMock('oxpsModulesConfigContent', array('__call'));
    }


    public function testGetModulesList()
    {
        // Config mock
        $oConfig = $this->getMock('oxConfig', array('getModulesDir'));
        $oConfig->expects($this->once())->method('getModulesDir')->will($this->returnValue('/shop/modules/'));

        oxRegistry::set('oxConfig', $oConfig);

        // Modules list mock
        $oModuleList = $this->getMock('oxModuleList', array('__call', 'getModulesFromDir'));
        $oModuleList->expects($this->once())->method('getModulesFromDir')->with('/shop/modules/')->will(
            $this->returnValue(
                array(
                    'my_module'         => (object) array('version' => '1.0.0'),
                    'oxpsmodulesconfig' => (object) array('version' => '0.1.0'),
                    'good_extension'    => (object) array('version' => '0.2.5'),
                )
            )
        );

        oxTestModules::addModuleObject('oxModuleList', $oModuleList);

        $this->assertEquals(
            array(
                'my_module'      => (object) array('version' => '1.0.0'),
                'good_extension' => (object) array('version' => '0.2.5'),
            ),
            $this->SUT->getModulesList()
        );
    }


    public function testGetSettingsList()
    {
        $mSettings = $this->SUT->getSettingsList();

        $this->assertTrue(is_array($mSettings));
        $this->assertArrayHasKey('version', $mSettings);
        $this->assertArrayHasKey('extend', $mSettings);
        $this->assertArrayHasKey('files', $mSettings);
        $this->assertArrayHasKey('templates', $mSettings);
        $this->assertArrayHasKey('blocks', $mSettings);
        $this->assertArrayHasKey('settings', $mSettings);
        $this->assertArrayHasKey('events', $mSettings);
    }
}
