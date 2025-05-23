<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\ProductAlert\Model\ResourceModel\Price as PriceResource;
use Magento\ProductAlert\Model\PriceFactory;
use Magento\Store\Model\StoreManagerInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;


$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $peoductRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productRepository->cleanCache();
/** @var PriceFactory $priceFactory */
$priceFactory = $objectManager->get(PriceFactory::class);
/** @var PriceResource $stockResource */
$stockResource = $objectManager->get(PriceResource::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
$secondWebsite = $storeManager->getWebsite('test');
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
$customer = $customerRepository->get('customer_second_ws_with_addr@example.com', (int)$secondWebsite->getId());
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

try {
    $productRepository->deleteById('simple_on_second_website_for_price_alert');
} catch (NoSuchEntityException $e) {
    //already removed
}


$priceAlert = $priceFactory->create();
$priceAlert->deleteCustomer((int)$customer->getId(), (int)$secondWebsite->getId());

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);

Resolver::getInstance()
    ->requireDataFixture('Magento/Customer/_files/customer_for_second_website_with_address_rollback.php');
