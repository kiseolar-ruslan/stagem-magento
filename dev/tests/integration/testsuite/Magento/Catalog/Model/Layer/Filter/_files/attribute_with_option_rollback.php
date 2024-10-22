<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* Create attribute */
/** @var $installer \Magento\Catalog\Setup\CategorySetup */
$installer = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Setup\CategorySetup::class
);
/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->loadByCode(\Magento\Catalog\Model\Product::ENTITY, 'attribute_with_option');

/* Delete simple products per each option */
/** @var $options \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection */
$options = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection::class
);
$registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
$options->setAttributeFilter($attribute->getId());

foreach ($options as $option) {
    /** @var $product \Magento\Catalog\Model\Product */
    $product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
        \Magento\Catalog\Model\Product::class
    );
    $product = $product->loadByAttribute('sku', 'simple_product_' . $option->getId());
    if ($product instanceof \Magento\Catalog\Model\Product && $product->getId()) {
        $product->delete();
    }
}

if ($attribute->getId()) {
    $attribute->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
