<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Helper;

/**
 * @magentoAppArea adminhtml
 * @magentoAppIsolation enabled
 * @magentoDbIsolation enabled
 */
class DataTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Backend\Model\Auth
     */
    protected $_auth;

    protected function setUp(): void
    {
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Config\ScopeInterface::class
        )->setCurrentScope(
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        );
        $this->_helper = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Helper\Data::class
        );
    }

    protected function tearDown(): void
    {
        $this->_helper = null;
        $this->_auth = null;
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Config\ScopeInterface::class
        )->setCurrentScope(
            null
        );
    }

    /**
     * Performs user login
     */
    protected function _login()
    {
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\UrlInterface::class
        )->turnOffSecretKey();
        $this->_auth = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\Auth::class
        );
        $this->_auth->login(
            \Magento\TestFramework\Bootstrap::ADMIN_NAME,
            \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD
        );
    }

    /**
     * Performs user logout
     */
    protected function _logout()
    {
        $this->_auth->logout();
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\UrlInterface::class
        )->turnOnSecretKey();
    }

    /**
     * @covers \Magento\Backend\Helper\Data::getPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::setPageHelpUrl
     * @covers \Magento\Backend\Helper\Data::addPageHelpUrl
     */
    public function testPageHelpUrl()
    {
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\App\RequestInterface::class
        )->setControllerModule(
            'dummy'
        )->setControllerName(
            'index'
        )->setActionName(
            'test'
        );

        $expected = 'http://www.magentocommerce.com/gethelp/en_US/dummy/index/test/';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url');

        $this->_helper->addPageHelpUrl('dummy');
        $expected .= 'dummy';
        $this->assertEquals($expected, $this->_helper->getPageHelpUrl(), 'Incorrect help Url suffix');
    }

    /**
     * @covers \Magento\Backend\Helper\Data::getCurrentUserId
     */
    public function testGetCurrentUserId()
    {
        $this->assertFalse($this->_helper->getCurrentUserId());

        /**
         * perform login
         */
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\UrlInterface::class
        )->turnOffSecretKey();

        $auth = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Backend\Model\Auth::class);
        $auth->login(\Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
        $this->assertEquals(1, $this->_helper->getCurrentUserId());

        /**
         * perform logout
         */
        $auth->logout();
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Backend\Model\UrlInterface::class
        )->turnOnSecretKey();

        $this->assertFalse($this->_helper->getCurrentUserId());
    }

    /**
     * @covers \Magento\Backend\Helper\Data::prepareFilterString
     */
    public function testPrepareFilterString()
    {
        $expected = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];

        $filterString = base64_encode('key1=' . rawurlencode('val1') . '&key2=' . rawurlencode('val2') . '&key3=val3');
        $actual = $this->_helper->prepareFilterString($filterString);
        $this->assertEquals($expected, $actual);
    }

    public function testGetHomePageUrl()
    {
        $this->assertStringEndsWith(
            'index.php/backend/admin/',
            $this->_helper->getHomePageUrl(),
            'Incorrect home page URL'
        );
    }
}
