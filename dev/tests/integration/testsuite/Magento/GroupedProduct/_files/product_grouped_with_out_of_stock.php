<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_associated.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual_in_stock.php');
Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_virtual_out_of_stock.php');

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$productRepository = $objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

/** @var $product \Magento\Catalog\Model\Product */
$product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Catalog\Model\Product::class);
$product->isObjectNew(true);
$product->setTypeId(
    \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE
)->setAttributeSetId(
    4
)->setWebsiteIds(
    [1]
)->setName(
    'Grouped Product'
)->setSku(
    'grouped-product'
)->setPrice(
    100
)->setTaxClassId(
    0
)->setVisibility(
    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH
)->setStatus(
    \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
);

$newLinks = [];
$productLinkFactory = $objectManager->get(\Magento\Catalog\Api\Data\ProductLinkInterfaceFactory::class);

/** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
$productLink = $productLinkFactory->create();
$linkedProduct = $productRepository->getById(1);
$productLink->setSku($product->getSku())
    ->setLinkType('associated')
    ->setLinkedProductSku($linkedProduct->getSku())
    ->setLinkedProductType($linkedProduct->getTypeId())
    ->setPosition(1)
    ->getExtensionAttributes()
    ->setQty(1);
$newLinks[] = $productLink;

$subProductsSkus = ['virtual-product', 'virtual-product-out'];
foreach ($subProductsSkus as $sku) {
    /** @var \Magento\Catalog\Api\Data\ProductLinkInterface $productLink */
    $productLink = $productLinkFactory->create();
    $linkedProduct = $productRepository->get($sku);
    $productLink->setSku($product->getSku())
        ->setLinkType('associated')
        ->setLinkedProductSku($sku)
        ->setLinkedProductType($linkedProduct->getTypeId())
        ->getExtensionAttributes()
        ->setQty(2.5);
    $newLinks[] = $productLink;
}
$product->setProductLinks($newLinks);
$product->save();

/** @var \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement */
$categoryLinkManagement = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                       ->create(\Magento\Catalog\Api\CategoryLinkManagementInterface::class);

$categoryLinkManagement->assignProductToCategories(
    $product->getSku(),
    [2]
);
