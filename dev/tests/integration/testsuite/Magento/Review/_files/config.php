<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Framework\App\Config\Value;
use Magento\TestFramework\App\Config as AppConfig;

/** @var Value $config */
$config = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(Value::class);
$config->setPath('catalog/review/allow_guest');
$config->setScope('default');
$config->setScopeId(0);
$config->setValue(1);
$config->save();

/** @var AppConfig $appConfig */
$appConfig = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(AppConfig::class);
$appConfig->clean();
