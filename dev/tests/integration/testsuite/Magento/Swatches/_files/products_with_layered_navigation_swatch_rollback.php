<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Framework\Registry $registry */
$registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(\Magento\Catalog\Api\ProductRepositoryInterface::class);

foreach (['simple1', 'simple2', 'simple3'] as $sku) {
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
        //Product already removed
    }
}

$productCollection = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                  ->get(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
foreach ($productCollection as $product) {
    $product->delete();
}

/** @var $category \Magento\Catalog\Model\Category */
$category = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Category::class);
$category->load(333);
if ($category->getId()) {
    $category->delete();
}

/** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
$attribute = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                          ->create(\Magento\Catalog\Model\ResourceModel\Eav\Attribute::class);

$attribute->loadByCode(4, 'color_swatch');

if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
