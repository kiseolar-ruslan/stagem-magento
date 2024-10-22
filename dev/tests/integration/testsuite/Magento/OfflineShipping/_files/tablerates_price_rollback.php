<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$resourceModel = $objectManager->create(Tablerate::class);
$resourceModel->getConnection()->delete($resourceModel->getMainTable());
