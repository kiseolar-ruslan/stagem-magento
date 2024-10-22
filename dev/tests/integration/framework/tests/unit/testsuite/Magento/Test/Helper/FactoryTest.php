<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Test\Helper;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetHelper()
    {
        $helper = \integration\framework\Magento\TestFramework\Helper\Factory::getHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class);
        $this->assertNotEmpty($helper);

        $helperNew = \integration\framework\Magento\TestFramework\Helper\Factory::getHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class);
        $this->assertSame($helper, $helperNew, 'Factory must cache instances of helpers.');
    }

    public function testSetHelper()
    {
        $helper = new \stdClass();
        \integration\framework\Magento\TestFramework\Helper\Factory::setHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class, $helper);
        $helperGot = \integration\framework\Magento\TestFramework\Helper\Factory::getHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class);
        $this->assertSame($helper, $helperGot, 'The helper must be used, when requested again');

        $helperNew = new \stdClass();
        \integration\framework\Magento\TestFramework\Helper\Factory::setHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class, $helperNew);
        $helperGot = \integration\framework\Magento\TestFramework\Helper\Factory::getHelper(
            \integration\framework\Magento\TestFramework\Helper\Config::class);
        $this->assertSame($helperNew, $helperGot, 'The helper must be changed upon new setHelper() method');
    }
}
