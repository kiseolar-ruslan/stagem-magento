<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductExtensionFactory;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\ConfigurableProduct\Helper\Product\Options\Factory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Swatches/_files/product_text_swatch_attribute.php');
Resolver::getInstance()->requireDataFixture('Magento/Swatches/_files/product_visual_swatch_attribute.php');

/** @var ObjectManagerInterface $objectManager */
$objectManager = Bootstrap::getObjectManager();
$installer = $objectManager->create(CategorySetup::class);
/** @var ProductAttributeRepositoryInterface $productAttributeRepository */
$productAttributeRepository = $objectManager->get(ProductAttributeRepositoryInterface::class);
$attribute = $productAttributeRepository->get('text_swatch_attribute');
$secondAttribute = $productAttributeRepository->get('visual_swatch_attribute');
$options = $attribute->getOptions();
$secondAttributeOptions = $secondAttribute->getOptions();
/** @var WebsiteRepositoryInterface $websiteRepository */
$websiteRepository = $objectManager->get(WebsiteRepositoryInterface::class);
$baseWebsite = $websiteRepository->get('base');
/** @var ProductAttributeRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$attributeValues = [];
$secondAttributeValues = [];
$associatedProductIds = [];
$associatedProductIdsViaSecondAttribute = [];
$attributeSetId = $installer->getAttributeSetId(Product::ENTITY, 'Default');
$productFactory = $objectManager->get(ProductFactory::class);
$rootCategoryId = $baseWebsite->getDefaultStore()->getRootCategoryId();
array_shift($options);
array_shift($secondAttributeOptions);

foreach ($options as $option) {
    foreach ($secondAttributeOptions as $secondAttrOption) {
        $product = $productFactory->create();
        $product->setTypeId(ProductType::TYPE_SIMPLE)
            ->setAttributeSetId($product->getDefaultAttributeSetId())
            ->setWebsiteIds([$baseWebsite->getId()])
            ->setName('Configurable Option ' . $option->getLabel())
            ->setSku(
                strtolower(
                    str_replace(' ', '_', 'simple ' . $option->getLabel() . '_' . $secondAttrOption->getLabel())
                )
            )
            ->setPrice(150)
            ->setTextSwatchAttribute($option->getValue())
            ->setVisualSwatchAttribute($secondAttrOption->getValue())
            ->setVisibility(Visibility::VISIBILITY_NOT_VISIBLE)
            ->setStatus(Status::STATUS_ENABLED)
            ->setCategoryIds([$rootCategoryId])
            ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1]);
        $product = $productRepository->save($product, true);
        $associatedProductIds[] = $product->getId();
    }

    $attributeValues[] = [
        'label' => 'test1',
        'attribute_id' => $attribute->getId(),
        'value_index' => $option->getValue(),
    ];
}
foreach ($secondAttributeOptions as $secondAttrOption) {
    $secondAttributeValues[] = [
        'label' => 'test2',
        'attribute_id' => $secondAttribute->getId(),
        'value_index' => $secondAttrOption->getValue(),
    ];
}

$allAttributes = [$attribute, $secondAttribute];
$optionsFactory = $objectManager->get(Factory::class);

foreach ($allAttributes as $attribute) {
    $configurableAttributesData[] =
        [
            'attribute_id' => $attribute->getId(),
            'code' => $attribute->getAttributeCode(),
            'label' => $attribute->getStoreLabel(),
            'position' => '0',
            'values' => $attribute->getAttributeCode() === 'text_swatch_attribute'
                ? $attributeValues
                : $secondAttributeValues,
        ];

}

$configurableOptions = $optionsFactory->create($configurableAttributesData);
$product = $productFactory->create();
/** @var ProductExtensionFactory $extensionAttributesFactory */
$extensionAttributesFactory = $objectManager->get(ProductExtensionFactory::class);
$extensionConfigurableAttributes = $product->getExtensionAttributes() ?: $extensionAttributesFactory->create();
$extensionConfigurableAttributes->setConfigurableProductOptions($configurableOptions);
$extensionConfigurableAttributes->setConfigurableProductLinks($associatedProductIds);
$product->setExtensionAttributes($extensionConfigurableAttributes);

$product->setTypeId(Configurable::TYPE_CODE)
    ->setAttributeSetId($product->getDefaultAttributeSetId())
    ->setWebsiteIds([$baseWebsite->getId()])
    ->setName('Configurable Product')
    ->setSku('configurable')
    ->setVisibility(Visibility::VISIBILITY_BOTH)
    ->setStatus(Status::STATUS_ENABLED)
    ->setCategoryIds([$rootCategoryId])
    ->setStockData(['use_config_manage_stock' => 1, 'is_in_stock' => 1]);
$productRepository->save($product);
