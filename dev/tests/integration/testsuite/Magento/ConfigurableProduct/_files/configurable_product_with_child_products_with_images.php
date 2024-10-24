<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\Store;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/product_image.php');
Resolver::getInstance()->requireDataFixture('Magento/ConfigurableProduct/_files/product_configurable.php');

$objectManager = Bootstrap::getObjectManager();
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$firstSimple = $productRepository->get('simple_10');
$secondSimple = $productRepository->get('simple_20');
/** @var $firstSimple Product */
$firstSimple->setStoreId(Store::DEFAULT_STORE_ID)
    ->setImage('/m/a/magento_image.jpg')
    ->setSmallImage('/m/a/magento_image.jpg')
    ->setThumbnail('/m/a/magento_image.jpg')
    ->setData(
        'media_gallery',
        [
            'images' => [
                [
                    'file' => '/m/a/magento_image.jpg',
                    'position' => 1,
                    'label' => 'Image Alt Text',
                    'disabled' => 0,
                    'media_type' => 'image'
                ],
            ]
        ]
    )
    ->setCanSaveCustomOptions(true)
    ->save();
/** @var $secondSimple Product */
$secondSimple->setStoreId(Store::DEFAULT_STORE_ID)
    ->setImage('/m/a/magento_thumbnail.jpg')
    ->setSmallImage('/m/a/magento_thumbnail.jpg')
    ->setThumbnail('/m/a/magento_thumbnail.jpg')
    ->setSwatchImage('/m/a/magento_thumbnail.jpg')
    ->setData(
        'media_gallery',
        [
            'images' => [
                [
                    'file' => '/m/a/magento_thumbnail.jpg',
                    'position' => 2,
                    'label' => 'Thumbnail Image',
                    'disabled' => 0,
                    'media_type' => 'image'
                ],
            ]
        ]
    )
    ->setCanSaveCustomOptions(true)
    ->save();
