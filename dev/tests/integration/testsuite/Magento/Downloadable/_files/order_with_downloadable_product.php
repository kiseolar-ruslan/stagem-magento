<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

$billingAddress = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Sales\Model\Order\Address::class,
    [
        'data' => [
            'firstname' => 'guest',
            'lastname' => 'guest',
            'email' => 'customer@example.com',
            'street' => 'street',
            'city' => 'Los Angeles',
            'region' => 'CA',
            'postcode' => '1',
            'country_id' => 'US',
            'telephone' => '1',
        ]
    ]
);
$billingAddress->setAddressType('billing');

$payment = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Sales\Model\Order\Payment::class
);
$payment->setMethod('checkmo');

/** @var \Magento\Sales\Model\Order\Item $orderItem */
$orderItem = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Sales\Model\Order\Item::class
);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                  ->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
$product = $productRepository->getById(1);
$link = $product->getExtensionAttributes()->getDownloadableProductLinks()[0];

$orderItem->setProductId(
    1
)->setProductType(
    \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE
)->setProductOptions(
    ['links' => [$link->getId()]]
)->setBasePrice(
    100
)->setQtyOrdered(
    1
);

$order = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Sales\Model\Order::class);
$order->setCustomerEmail(
    'mail@to.co'
)->addItem(
    $orderItem
)->setIncrementId(
    '100000001'
)->setCustomerIsGuest(
    true
)->setStoreId(
    1
)->setEmailSent(
    1
)->setBillingAddress(
    $billingAddress
)->setPayment(
    $payment
);
$order->save();
