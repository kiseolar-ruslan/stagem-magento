<?php
/**
 * Fixture for Customer List method.
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

use Magento\Customer\Model\Customer;
use Magento\Store\Model\Store;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$customer = Bootstrap::getObjectManager()->create(
    Customer::class
);
$customer->setWebsiteId(1)
    ->setEmail('customer_two@example.com')
    ->setPassword('password')
    ->setGroupId(2)
    ->setStoreId(Store::DEFAULT_STORE_ID)
    ->setIsActive(1)
    ->setFirstname('Firstname')
    ->setLastname('Lastname')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1);

$customer->isObjectNew(true);
$customer->save();
