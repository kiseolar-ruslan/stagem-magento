<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Customer\Model\CustomerRegistry;

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var $repository \Magento\Customer\Api\CustomerRepositoryInterface */
$repository = $objectManager->create(\Magento\Customer\Api\CustomerRepositoryInterface::class);
$customer = $objectManager->create(\Magento\Customer\Model\Customer::class);
/** @var CustomerRegistry $customerRegistry */
$customerRegistry = $objectManager->get(CustomerRegistry::class);
/** @var Magento\Customer\Model\Customer $customer */
$customer->setWebsiteId(1)
    ->setId(1)
    ->setEmail('customer@example.com')
    ->setGroupId(1)
    ->setStoreId(1)
    ->setIsActive(1)
    ->setPrefix('Mr.')
    ->setFirstname('John')
    ->setMiddlename('A')
    ->setLastname('Smith')
    ->setSuffix('Esq.')
    ->setDefaultBilling(1)
    ->setDefaultShipping(1)
    ->setTaxvat('12')
    ->setGender(0);

$customer->isObjectNew(true);
$customer->save();
$customerRegistry->remove($customer->getId());
/** @var \Magento\JwtUserToken\Api\RevokedRepositoryInterface $revokedRepo */
$revokedRepo = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                            ->get(\Magento\JwtUserToken\Api\RevokedRepositoryInterface::class);
$revokedRepo->saveRevoked(
    new \Magento\JwtUserToken\Api\Data\Revoked(
        \Magento\Authorization\Model\UserContextInterface::USER_TYPE_CUSTOMER,
        (int) $customer->getId(),
        time() - 3600 * 24
    )
);
