<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\TestFramework\Directory\Model\RemoveCurrencyRateByCode;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
/** @var RemoveCurrencyRateByCode $deleteRateByCode */
$deleteRateByCode = $objectManager->get(RemoveCurrencyRateByCode::class);
$deleteRateByCode->execute('UAH');
