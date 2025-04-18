<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @deprecated use next @magentoConfigFixture instead:
 * @magentoConfigFixture default_store carriers/flatrate/active 0
 * @magentoConfigFixture default_store carriers/tablerate/active 0
 * @magentoConfigFixture default_store carriers/freeshipping/active 0
 */
declare(strict_types=1);

use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\ScopeConfigInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var Writer $configWriter */
$configWriter = $objectManager->get(WriterInterface::class);

$configWriter->save('carriers/flatrate/active', 0);
$configWriter->save('carriers/tablerate/active', 0);
$configWriter->save('carriers/freeshipping/active', 0);

$scopeConfig = $objectManager->get(ScopeConfigInterface::class);
$scopeConfig->clean();
