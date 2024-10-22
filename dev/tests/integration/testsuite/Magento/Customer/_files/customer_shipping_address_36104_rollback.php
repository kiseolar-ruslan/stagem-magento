<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Framework\Registry $registry */
$registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Customer\Model\Address $customerAddress */
$customerAddress = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                ->create(\Magento\Customer\Model\Address::class);
$customerAddress->load(1);
if ($customerAddress->getId()) {
    $customerAddress->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
