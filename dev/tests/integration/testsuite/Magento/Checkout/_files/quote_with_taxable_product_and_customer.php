<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Type\Onepage;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\AddressInterfaceFactory;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartInterfaceFactory;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/taxable_simple_product.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
$productRepository->cleanCache();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
/** @var AddressInterface $quoteShippingAddress */
$quoteShippingAddress = $objectManager->get(AddressInterfaceFactory::class)->create();
/** @var CustomerRepositoryInterface $customerRepository */
$customerRepository = $objectManager->get(CustomerRepositoryInterface::class);
/** @var AddressRepositoryInterface $addressRepository */
$addressRepository = $objectManager->get(AddressRepositoryInterface::class);
$quoteShippingAddress->importCustomerAddressData($addressRepository->getById(1));
$customer = $customerRepository->getById(1);

/** @var CartInterface $quote */
$quote = $objectManager->get(CartInterfaceFactory::class)->create();
$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(0)
    ->assignCustomerWithAddressChange($customer)
    ->setShippingAddress($quoteShippingAddress)
    ->setBillingAddress($quoteShippingAddress)
    ->setCheckoutMethod(Onepage::METHOD_CUSTOMER)
    ->setReservedOrderId('test_order_with_taxable_product')
    ->setEmail($customer->getEmail())
    ->addProduct($productRepository->get('taxable_product'), 1);
$quoteRepository->save($quote);
