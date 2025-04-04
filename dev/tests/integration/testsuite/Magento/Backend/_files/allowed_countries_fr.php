<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

use Magento\Framework\App\Config\ConfigResource\ConfigInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var ConfigInterface $config */
$config = $objectManager->get(ConfigInterface::class);
$config->saveConfig('general/country/allow', 'FR');
$objectManager->get(ReinitableConfigInterface::class)->reinit();
