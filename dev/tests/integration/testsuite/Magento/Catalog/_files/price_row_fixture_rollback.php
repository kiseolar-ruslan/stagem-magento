<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\Framework\Exception\NoSuchEntityException;

/** @var \Magento\Framework\Registry $registry */
$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$registry = $objectManager->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

$repository = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ProductRepository::class
);
try {
    $product = $repository->get('simple', false, null, true);
    $product->delete();
} catch (NoSuchEntityException $e) {
    //Entity already deleted
}

/** @var $category \Magento\Catalog\Model\Category */
$category = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Category::class);
$category->load(9)->delete();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
