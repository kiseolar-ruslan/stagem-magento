<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productRepository->cleanCache();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
$customer = $customerRepository->get('customer@example.com');

/** @var CartInterface $quote */
$quote = $objectManager->get(CartInterfaceFactory::class)->create();
$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(0)
    ->setCustomer($customer)
    ->setCheckoutMethod(Onepage::METHOD_CUSTOMER)
    ->setReservedOrderId('test_order_with_customer_without_address')
    ->addProduct($productRepository->get('simple2'), 1);
$quoteRepository->save($quote);
