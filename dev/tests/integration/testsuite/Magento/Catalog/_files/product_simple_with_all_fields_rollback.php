<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_simple_rollback.php');

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Framework\Registry $registry */
$registry = $objectManager->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

//Remove categories
/** @var Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
$collection = $objectManager->create(\Magento\Catalog\Model\ResourceModel\Category\Collection::class);
foreach ($collection->addAttributeToFilter('level', ['in' => [2, 3, 4]]) as $category) {
    /** @var \Magento\Catalog\Model\Category $category */
    $category->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
