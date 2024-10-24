<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Model\Config\Backend\Admin;

use Magento\Config\Model\Config\Reader\Source\Deployed\DocumentRoot;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

/**
 * @magentoAppArea adminhtml
 */
class RobotsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Config\Model\Config\Backend\Admin\Robots
     */
    protected $model = null;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $rootDirectory;

    /**
     * Initialize model
     */
    protected function setUp(): void
    {
        parent::setUp();

        $objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->model = $objectManager->create(\Magento\Config\Model\Config\Backend\Admin\Robots::class);
        $this->model->setPath('design/search_engine_robots/custom_instructions');
        $this->model->afterLoad();

        $this->rootDirectory = $objectManager->get(Filesystem::class)->getDirectoryRead(DirectoryList::PUB);
    }

    /**
     * Check that default value is empty when robots.txt not exists
     *
     * @magentoDataFixture Magento/Config/Model/_files/no_robots_txt.php
     */
    public function testAfterLoadRobotsTxtNotExists()
    {
        $this->assertEmpty($this->model->getValue());
    }

    /**
     * Check that default value equals to robots.txt content when it is available
     *
     * @magentoDataFixture Magento/Config/Model/_files/robots_txt.php
     */
    public function testAfterLoadRobotsTxtExists()
    {
        $value = $this->model->getValue();
        $this->assertEquals('Sitemap: http://store.com/sitemap.xml', $value);
    }

    /**
     * Check robots.txt file generated when robots.txt not exists
     *
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileNotExists()
    {
        $this->assertFalse($this->rootDirectory->isExist('robots.txt'), 'robots.txt exists');

        $this->_modifyConfig();
    }

    /**
     * Check robots.txt file changed when robots.txt exists
     *
     * @magentoDataFixture Magento/Config/Model/_files/robots_txt.php
     * @magentoDbIsolation enabled
     */
    public function testAfterSaveFileExists()
    {
        $this->assertTrue($this->rootDirectory->isExist('robots.txt'), 'robots.txt not exists');

        $this->_modifyConfig();
    }

    /**
     * Modify config value and check all changes were written into robots.txt
     */
    protected function _modifyConfig()
    {
        $robotsTxt = "User-Agent: *\nDisallow: /checkout";
        $this->model->setValue($robotsTxt)->save();
        $file = $this->rootDirectory->getAbsolutePath('robots.txt');
        $this->assertStringEqualsFile($file, $robotsTxt);
    }

    /**
     * Remove created robots.txt
     */
    protected function tearDown(): void
    {
        require 'Magento/Config/Model/_files/no_robots_txt.php';
    }
}
