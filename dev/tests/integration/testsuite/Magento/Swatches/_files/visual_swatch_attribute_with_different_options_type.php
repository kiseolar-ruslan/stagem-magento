<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Eav\Api\Data\AttributeOptionInterface;
use Magento\Setup\Fixtures\ImagesGenerator\ImagesGenerator;
use Magento\Swatches\Helper\Media as SwatchesMedia;
use Magento\Swatches\Model\Swatch;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

/** @var $installer CategorySetup */
$installer = Bootstrap::getObjectManager()->create(\Magento\Catalog\Setup\CategorySetup::class);
/** @var AttributeRepositoryInterface $attributeRepository */
$attributeRepository = Bootstrap::getObjectManager()->create(AttributeRepositoryInterface::class);

// Generate swatch image
/** @var ImagesGenerator $imagesGenerator */
$imagesGenerator = Bootstrap::getObjectManager()->get(ImagesGenerator::class);
/** @var SwatchesMedia $swatchesMedia */
$swatchesMedia = Bootstrap::getObjectManager()->get(SwatchesMedia::class);
$imageName = '/visual_swatch_attribute_option_type_image.jpg';
$imagesGenerator->generate([
    'image-width' => 110,
    'image-height' => 90,
    'image-name' => $imageName,
]);
$imagePath = substr($swatchesMedia->moveImageFromTmp($imageName), 1);
$swatchesMedia->generateSwatchVariations($imagePath);

// Add attribute data
$data = [
    'attribute_code' => 'test_configurable',
    'entity_type_id' => $installer->getEntityTypeId('catalog_product'),
    'is_global' => 1,
    'is_user_defined' => 1,
    'frontend_input' => 'select',
    'is_unique' => 0,
    'is_required' => 0,
    'is_searchable' => 0,
    'is_visible_in_advanced_search' => 0,
    'is_comparable' => 0,
    'is_filterable' => 0,
    'is_filterable_in_search' => 0,
    'is_used_for_promo_rules' => 0,
    'is_html_allowed_on_front' => 1,
    'is_visible_on_front' => 0,
    'used_in_product_listing' => 0,
    'used_for_sort_by' => 0,
    'frontend_label' => ['Test Configurable'],
    'backend_type' => 'int',
];

$optionsPerAttribute = 3;

$data['frontend_input'] = 'select';
$data['swatch_input_type'] = Swatch::SWATCH_INPUT_TYPE_VISUAL;

$data['swatchvisual']['value'] = [
    'option_1' => '#000000', // HEX color (color type)
    'option_2' => $imagePath, // image path (image type)
    'option_3' => null, // null (empty type)
];

$data['optionvisual']['value'] = array_reduce(
    range(1, $optionsPerAttribute),
    function ($values, $index) use ($optionsPerAttribute) {
        $values['option_' . $index] = ['option ' . $index];
        return $values;
    },
    []
);

$data['optionvisual']['order'] = array_reduce(
    range(1, $optionsPerAttribute),
    function ($values, $index) use ($optionsPerAttribute) {
        $values['option_' . $index] = $index;
        return $values;
    },
    []
);

$data['options']['option'] = array_reduce(
    range(1, $optionsPerAttribute),
    function ($values, $index) use ($optionsPerAttribute) {
        $values[] = [
            'label' => 'option ' . $index,
            'value' => 'option_' . $index,
        ];
        return $values;
    },
    []
);

$options = [];
foreach ($data['options']['option'] as $optionData) {
    $options[] = Bootstrap::getObjectManager()->create(AttributeOptionInterface::class)
        ->setLabel($optionData['label'])
        ->setValue($optionData['value']);
}

$attribute = Bootstrap::getObjectManager()->create(
    ProductAttributeInterface::class,
    ['data' => $data]
);

$attribute->setOptions($options);
$attributeRepository->save($attribute);

/* Assign attribute to attribute set */
$installer->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute->getId());
