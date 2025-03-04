<?php
/**
 * Server_processes_manager module automated integration tests
 *
 * PHP Version 7
 *
 * @category Test
 * @package  Loris
 * @author   Wang Shen <wangshen.mcin@gmail.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://github.com/aces/Loris
 */
use Facebook\WebDriver\WebDriverBy;
require_once __DIR__ .
    "/../../../test/integrationtests/LorisIntegrationTest.class.inc";

/**
 * Server_processes_manager module automated integration tests
 *
 * PHP Version 7
 *
 * @category Test
 * @package  Loris
 * @author   Wang Shen <wangshen.mcin@gmail.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.txt GPLv3
 * @link     https://github.com/aces/Loris
 */
class Server_Processes_ManagerTest extends LorisIntegrationTest
{
    /* Set mriCodePath to a valid, readable path. The integration tests
     * don't actually interact with the LORIS-MRI libraries so /etc/ works
     * as a dummy value since Travis should always have this directory.
     *
     * @var string
     */
    const MRI_CODE_PATH = '/etc/';

    /**
     * UI elements and locations
     * breadcrumb - ''
     * Table headers
     */
    private $_loadingUI
        =  [
            'Server Processes Manager' => '#bc2 > a:nth-child(2) > div',
            //table headers
            'No.'                      => '#dynamictable > thead > tr',
            'PID'                      => '#dynamictable > thead > tr',
            'Type'                     => '#dynamictable > thead > tr',
            'Stdout File'              => '#dynamictable > thead > tr',
            'Stderr File'              => '#dynamictable > thead > tr',
            'Exit Code File'           => '#dynamictable > thead > tr',
            'Exit Code'                => '#dynamictable > thead > tr',
            'User ID'                  => '#dynamictable > thead > tr',
            'Start Time'               => '#dynamictable > thead > tr',
            'End Time'                 => '#dynamictable > thead > tr',
        ];

    //Filter locations
    static $pid    = 'input[name="pid"]';
    static $type   = 'input[name="type"]';
    static $userid = 'input[name="userid"]';
    //General locations
    static $display     = '.table-header > div > div > div:nth-child(1)';
    static $clearFilter = '.nav-tabs a';

    /**
     * Tests that the page does not load if config setting mriCodePath has
     * not been set.
     *
     * @return void
     */
    function testDoesNotLoadWithoutMRICodePath()
    {
        $this->setupConfigSetting('mriCodePath', null);
        $this->setupPermissions(["server_processes_manager"]);
        $this->safeGet($this->url . "/server_processes_manager/");
        $bodyText = $this->safeFindElement(
            WebDriverBy::cssSelector("body")
        )->getText();
        $this->assertStringContainsString('Cannot continue', $bodyText);
        $this->resetPermissions();
        $this->restoreConfigSetting("mriCodePath");
    }

    /**
     * Tests that the page does not load if the user does not have correct
     * permissions
     *
     * @return void
     */
    function testLoadsWithoutPermissionRead()
    {
        $this->setupConfigSetting('mriCodePath', self::MRI_CODE_PATH);
        $this->setupPermissions([""]);
        $this->safeGet($this->url . "/server_processes_manager/");
        $bodyText = $this->safeFindElement(
            WebDriverBy::cssSelector("body")
        )->getText();
        $this->assertStringContainsString(
            "You do not have access to this page.",
            $bodyText
        );
        $this->resetPermissions();
        $this->restoreConfigSetting("mriCodePath");
    }
    /**
     * Tests that the page does not load if the user does not have correct
     * permissions
     *
     * @return void
     */
    function testDoesNotLoadWithPermission()
    {
        $this->setupConfigSetting('mriCodePath', self::MRI_CODE_PATH);
        $this->setupPermissions(["server_processes_manager"]);
        $this->safeGet($this->url . "/server_processes_manager/");
        $bodyText = $this->safeFindElement(
            WebDriverBy::cssSelector("body")
        )->getText();
        $this->assertStringNotContainsString(
            "You do not have access to this page.",
            $bodyText
        );
        $this->assertStringNotContainsString(
            "An error occured while loading the page.",
            $bodyText
        );
        $this->resetPermissions();
        $this->restoreConfigSetting("mriCodePath");
    }

    /**
     * Testing UI elements when page loads
     *
     * @return void
     */
    function testPageUIs()
    {
        $this->setupConfigSetting('mriCodePath', self::MRI_CODE_PATH);
        $this->safeGet($this->url . "/server_processes_manager/");
        foreach ($this->_loadingUI as $key => $value) {
            $text = $this->safeFindElement(
                WebDriverBy::cssSelector("$value")
            )->getText();
            $this->assertStringContainsString($key, $text);
        }
        $this->restoreConfigSetting("mriCodePath");
    }
    /**
     * Testing React filter in this page.
     *
     * @return void
     */
    function testFilters()
    {
        $this->setupConfigSetting('mriCodePath', self::MRI_CODE_PATH);
        $this->safeGet($this->url . "/server_processes_manager/");
        $this->_filterTest(
            self::$pid,
            self::$display,
            self::$clearFilter,
            '317',
            '1 row'
        );
        $this->_filterTest(
            self::$type,
            self::$display,
            self::$clearFilter,
            'mri_upload',
            '51'
        );
        $this->_filterTest(
            self::$userid,
            self::$display,
            self::$clearFilter,
            'admin',
            '51'
        );
        $this->restoreConfigSetting("mriCodePath");
    }
}

