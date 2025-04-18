<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\ScopeConfigInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var Writer $configWriter */
$configWriter = $objectManager->get(WriterInterface::class);

$configWriter->save('carriers/fedex/active', '1');
$configWriter->save('carriers/fedex/sandbox_mode', '1');
$configWriter->save(\Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP, '90210');

$scopeConfig = $objectManager->get(ScopeConfigInterface::class);
$scopeConfig->clean();
