<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\PaymentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\QuoteFactory;
use Magento\Quote\Model\ResourceModel\Quote as QuoteResource;
use Magento\Store\Model\StoreManagerInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

/** @var ObjectManager $objectManager */
$objectManager = Bootstrap::getObjectManager();
/** @var QuoteFactory $quoteFactory */
$quoteFactory = Bootstrap::getObjectManager()->get(QuoteFactory::class);
/** @var QuoteResource $quoteResource */
$quoteResource = Bootstrap::getObjectManager()->get(QuoteResource::class);
/** @var StoreManagerInterface $storeManager */
$storeManager = $objectManager->get(StoreManagerInterface::class);
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
$store = $storeManager->getStore();
/** @var Quote $quote */
$quote = $quoteFactory->create();
$quote->setReservedOrderId('multishipping_fpt_quote_id')
    ->setCustomerEmail('customer001@test.com')
    ->setStoreId($storeManager->getStore()->getId());

$shipping = [
    'firstname' => 'Jonh',
    'lastname' => 'Doe',
    'telephone' => '0333-233-221',
    'street' => ['Main Division 1'],
    'city' => 'Culver City',
    'region' => 'CA',
    'postcode' => 90800,
    'country_id' => 'US',
    'email' => 'customer001@shipping.test',
    'address_type' => 'shipping',
];
$methodCode = 'flatrate_flatrate';
    /** @var Rate $rate */
    $rate = $objectManager->create(Rate::class);
    $rate->setCode($methodCode)
        ->setPrice(5.00);

    $address = $objectManager->create(AddressInterface::class, ['data' => $shipping]);
    $address->setShippingMethod($methodCode)
        ->addShippingRate($rate)
        ->setShippingAmount(5.00)
        ->setBaseShippingAmount(5.00);

    $quote->addAddress($address);

/** @var AddressInterface $address */
$address = $objectManager->create(
    AddressInterface::class,
    [
        'data' => [
            'firstname' => 'Jonh',
            'lastname' => 'Doe',
            'telephone' => '0333-233-221',
            'street' => ['Third Division 1'],
            'city' => 'New York',
            'region' => 'NY',
            'postcode' => 10029,
            'country_id' => 'US',
            'email' => 'customer001@billing.test',
            'address_type' => 'billing',
        ],
    ]
);
$quote->setBillingAddress($address);

$quote->setIsMultiShipping(1);
$quoteRepository->save($quote);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
Resolver::getInstance()->requireDataFixture('Magento/Weee/_files/product_with_fpt.php');
/** @var Product $product */
$product = $productRepository->get('simple-with-ftp');

/** @var Item $item */
$item = $objectManager->create(Item::class);
$item->setProduct($product)
    ->setPrice($product->getPrice())
    ->setQty(2);
$quote->addItem($item);
$quoteRepository->save($quote);

$addressList = $quote->getAllShippingAddresses();
$address = reset($addressList);
$item->setQty(2);
$address->setTotalQty(2);
$address->addItem($item);
$quoteRepository->save($quote);

$quote = $quoteFactory->create();
$quoteResource->load($quote, 'multishipping_fpt_quote_id', 'reserved_order_id');
/** @var PaymentInterface $payment */
$payment = $objectManager->create(PaymentInterface::class);
$payment->setMethod('checkmo');
$quote->setPayment($payment);
$quote->collectTotals();
$quoteRepository->save($quote);
