<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Framework\Registry $registry */
$registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var $category \Magento\Catalog\Model\Category */
$category = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Category::class);
$category->load(333);
if ($category->getId()) {
    $category->delete();
}

/** @var $product \Magento\Catalog\Model\Product */
$product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->load(333);
if ($product->getId()) {
    $product->delete();
}

/** @var $product \Magento\Catalog\Model\Product */
$product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->load(334);
if ($product->getId()) {
    $product->delete();
}
