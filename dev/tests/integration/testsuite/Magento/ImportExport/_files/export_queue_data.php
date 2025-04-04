<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\ImportExport\Model\Export\Entity\ExportInfoFactory;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ExportInfoFactory $exportInfoFactory */
$exportInfoFactory = $objectManager->get(ExportInfoFactory::class);
/** @var PublisherInterface $messagePublisher */
$messagePublisher = $objectManager->get(PublisherInterface::class);
$dataObject = $exportInfoFactory->create(
    'csv',
    ProductAttributeInterface::ENTITY_TYPE_CODE,
    [ProductInterface::SKU => 'simple2']
);
$messagePublisher->publish('import_export.export', $dataObject);
