<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/products.php');

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                  ->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
/** @var $product \Magento\Catalog\Model\Product */
$product = $productRepository->get('simple');

$requestInfo = new \Magento\Framework\DataObject(['qty' => 1]);

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Checkout\Model\Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(\Magento\Checkout\Model\Session::class);
