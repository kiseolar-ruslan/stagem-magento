<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Autoload\AutoloaderRegistry;

require_once __DIR__ . '/../../../../app/bootstrap.php';
require_once __DIR__ . '/autoload.php';
//to handle different types of errors on CI
require __DIR__ . '/../../error_handler.php';

error_reporting(E_ALL);
$testsBaseDir = dirname(__DIR__);
$integrationTestsDir = realpath("{$testsBaseDir}/../integration");
$fixtureBaseDir = $integrationTestsDir . '/testsuite';

if (!defined('TESTS_BASE_DIR')) {
    define('TESTS_BASE_DIR', $testsBaseDir);
}

if (!defined('TESTS_TEMP_DIR')) {
    define('TESTS_TEMP_DIR', $testsBaseDir . '/tmp');
}

if (!defined('TESTS_MODULES_PATH')) {
    define('TESTS_MODULES_PATH', $testsBaseDir . '/_files');
}

if (!defined('MAGENTO_MODULES_PATH')) {
    define('MAGENTO_MODULES_PATH', __DIR__ . '/../../../../app/code/Magento/');
}

if (!defined('INTEGRATION_TESTS_BASE_DIR')) {
    define('INTEGRATION_TESTS_BASE_DIR', $integrationTestsDir);
}
$settings = new \Magento\TestFramework\Bootstrap\Settings($testsBaseDir, get_defined_constants());

try {
    setCustomErrorHandler();
    $installConfigFile = $settings->getAsConfigFile('TESTS_INSTALL_CONFIG_FILE');
    if (!file_exists($installConfigFile)) {
        $installConfigFile .= '.dist';
    }
    if (!defined('TESTS_INSTALLATION_DB_CONFIG_FILE')) {
        define('TESTS_INSTALLATION_DB_CONFIG_FILE', $installConfigFile);
    }
    /* Bootstrap the application */
    $shell = new \Magento\Framework\Shell(new \Magento\Framework\Shell\CommandRenderer());
    $testFrameworkDir = __DIR__;

    $globalConfigFile = $settings->getAsConfigFile('TESTS_GLOBAL_CONFIG_FILE');
    if (!file_exists($globalConfigFile)) {
        $globalConfigFile .= '.dist';
    }

    $dirList = new DirectoryList(BP);
    $installDir = TESTS_TEMP_DIR;
    $application = new \Magento\TestFramework\SetupApplication(
        $shell,
        $installDir,
        $installConfigFile,
        $globalConfigFile,
        $settings->get('TESTS_GLOBAL_CONFIG_DIR'),
        $settings->get('TESTS_MAGENTO_MODE'),
        AutoloaderRegistry::getAutoloader(),
        false
    );

    $bootstrap = new \Magento\TestFramework\Bootstrap(
        $settings,
        new \Magento\TestFramework\Bootstrap\Environment(),
        new \Magento\TestFramework\Bootstrap\SetupDocBlock("{$testsBaseDir}/_files/"),
        new \Magento\TestFramework\Bootstrap\Profiler(new \Magento\Framework\Profiler\Driver\Standard()),
        $shell,
        $application,
        new \Magento\TestFramework\Bootstrap\MemoryFactory($shell)
    );
    //remove test modules files
    include_once __DIR__ . '/../../setup-integration/framework/removeTestModules.php';
    $bootstrap->runBootstrap();
    $application->createInstallDir();
    //We do not want to install anything
    $application->initialize([]);

    if ($settings->getAsBoolean('TESTS_CLEANUP')) {
        $application->cleanup();
    }

    \integration\framework\Magento\TestFramework\Helper\Bootstrap::setInstance(new \integration\framework\Magento\TestFramework\Helper\Bootstrap($bootstrap));

    $dirSearch = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                              ->create(\Magento\Framework\Component\DirSearch::class);
    $themePackageList = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                     ->create(\Magento\Framework\View\Design\Theme\ThemePackageList::class);
    \Magento\Framework\App\Utility\Files::setInstance(
        new Magento\Framework\App\Utility\Files(
            new \Magento\Framework\Component\ComponentRegistrar(),
            $dirSearch,
            $themePackageList
        )
    );

    /* Unset declared global variables to release the PHPUnit from maintaining their values between tests */
    unset($testsBaseDir, $settings, $shell, $application, $bootstrap);
} catch (\Exception $e) {
    // phpcs:disable Magento2.Security.LanguageConstruct
    echo $e . PHP_EOL;
    exit(1);
    // phpcs:enable Magento2.Security.LanguageConstruct
}
