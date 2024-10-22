<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @deprecated use next @magentoConfigFixture instead:
 * @magentoConfigFixture default_store checkout/options/guest_checkout 0
 */
declare(strict_types=1);

use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Storage\WriterInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\Framework\App\Config\ScopeConfigInterface;

$objectManager = Bootstrap::getObjectManager();
/** @var Writer $configWriter */
$configWriter = $objectManager->get(WriterInterface::class);

$configWriter->save('checkout/options/guest_checkout', 0);

$scopeConfig = $objectManager->get(ScopeConfigInterface::class);
$scopeConfig->clean();
