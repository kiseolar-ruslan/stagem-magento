<?php
/**
 * Fixture for Customer List method.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$customer = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Customer\Model\Customer::class
);
$customer->setWebsiteId(1)
    ->setEntityId(2)
    ->setEntityTypeId(1)
    ->setAttributeSetId(0)
    ->setEmail('customer_two@example.com')
    ->setPassword('password')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setFirstname('Firstname')
    ->setLastname('Lastname')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);

$customer->isObjectNew(true);
$customer->save();
