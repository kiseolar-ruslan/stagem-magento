<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);

$product = $productRepository->get('simple_product');
$extensionAttributes = $product->getExtensionAttributes();
$stockItem = $extensionAttributes->getStockItem();
$stockItem->setIsInStock(false);
$productRepository->save($product);
