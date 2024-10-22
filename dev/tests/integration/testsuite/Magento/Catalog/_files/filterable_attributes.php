<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $installer \Magento\Catalog\Setup\CategorySetup */
$installer = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Setup\CategorySetup::class
);
$attributeSetId = $installer->getAttributeSetId('catalog_product', 'Default');
$entityModel = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Eav\Model\Entity::class);
$entityTypeId = $entityModel->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
$groupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

/** @var $attribute \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
$attribute = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setAttributeCode(
    'filterable_attribute_a'
)->setEntityTypeId(
    $entityTypeId
)->setAttributeGroupId(
    $groupId
)->setAttributeSetId(
    $attributeSetId
)->setIsFilterable(
    1
)->setIsUserDefined(
    1
)->save();

$attribute = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Catalog\Model\ResourceModel\Eav\Attribute::class
);
$attribute->setAttributeCode(
    'filterable_attribute_b'
)->setEntityTypeId(
    $entityTypeId
)->setAttributeGroupId(
    $groupId
)->setAttributeSetId(
    $attributeSetId
)->setIsFilterable(
    1
)->setIsUserDefined(
    1
)->save();
