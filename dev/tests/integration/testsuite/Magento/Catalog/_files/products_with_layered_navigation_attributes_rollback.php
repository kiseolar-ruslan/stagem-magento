<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\TestFramework\Catalog\Model\GetCategoryByName;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var Registry $registry */
$registry = $objectManager->get(Registry::class);
$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);

/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->get(ProductRepositoryInterface::class);
for ($i = 1; $i <= 6; $i++) {
    $sku = 'simple' . $i;
    try {
        $product = $productRepository->get($sku, false, null, true);
        $productRepository->delete($product);
    } catch (NoSuchEntityException $exception) {
        //Product already removed
    }
}

/** @var CategoryRepositoryInterface $categoryRepository */
$categoryRepository = $objectManager->get(CategoryRepositoryInterface::class);
/** @var GetCategoryByName $getCategoryByName */
$getCategoryByName = $objectManager->get(GetCategoryByName::class);
$category = $getCategoryByName->execute('Category 1');
try {
    if ($category->getId()) {
        $categoryRepository->delete($category);
    }
} catch (NoSuchEntityException $exception) {
    //Category already removed
}

$eavConfig = $objectManager->get(Config::class);
/** @var ProductAttributeRepositoryInterface $attributeRepository */
$attributeRepository = $objectManager->get(ProductAttributeRepositoryInterface::class);

try {
    for ($i = 1; $i <= 2; $i++) {
        $attribute = $attributeRepository->get('test_attribute_' . $i);
        $attributeRepository->delete($attribute);
    }
} catch (NoSuchEntityException $exception) {
    //Attribute already removed
}
$eavConfig->clear();

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
