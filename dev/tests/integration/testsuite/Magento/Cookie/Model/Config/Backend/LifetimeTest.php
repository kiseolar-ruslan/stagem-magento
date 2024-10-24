<?php
/**
 * Integration test for Magento\Cookie\Model\Config\Backend\Lifetime
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Cookie\Model\Config\Backend;

class LifetimeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Method is not publicly accessible, so it must be called through parent
     *
     */
    public function testBeforeSaveException()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage('Invalid cookie lifetime: must be numeric');

        $invalidCookieLifetime = 'invalid lifetime';
        $objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Cookie\Model\Config\Backend\Lifetime $model */
        $model = $objectManager->create(\Magento\Cookie\Model\Config\Backend\Lifetime::class);
        $model->setValue($invalidCookieLifetime);
        $model->save();
    }
}
