<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\Indexer\Stock\Processor;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Registry;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$registry = Bootstrap::getObjectManager()->get(Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$productRepository = Bootstrap::getObjectManager()
    ->get(ProductRepositoryInterface::class);

try {
    $product = $productRepository->get('virtual-product', false, null, true);
    $productRepository->delete($product);
} catch (NoSuchEntityException $exception) {
    //Product already removed
} catch (StateException $exception) {
}

ObjectManager::getInstance()->create(Processor::class)->reindexAll();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
