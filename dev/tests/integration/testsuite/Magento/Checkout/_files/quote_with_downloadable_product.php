<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Downloadable/_files/product_downloadable.php');

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                  ->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);
/** @var $product \Magento\Catalog\Model\Product */
$product = $productRepository->get('downloadable-product');

/** @var $linkCollection \Magento\Downloadable\Model\ResourceModel\Link\Collection */
$linkCollection = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Downloadable\Model\Link::class
)->getCollection()->addProductToFilter(
    $product->getId()
)->addTitleToResult(
    $product->getStoreId()
)->addPriceToResult(
    $product->getStore()->getWebsiteId()
);

/** @var $link \Magento\Downloadable\Model\Link */
$link = $linkCollection->getFirstItem();

$requestInfo = new \Magento\Framework\DataObject(['qty' => 1, 'links' => [$link->getId()]]);

/** @var $cart \Magento\Checkout\Model\Cart */
$cart = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Checkout\Model\Cart::class);
$cart->addProduct($product, $requestInfo);
$cart->save();

/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManager->removeSharedInstance(\Magento\Checkout\Model\Session::class);
