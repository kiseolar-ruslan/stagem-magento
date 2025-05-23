<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestModuleOverrideConfig\Model\FixtureCallStorage;

/** @var FixtureCallStorage $fixtureStorage */
$fixtureStorage = Bootstrap::getObjectManager()->get(FixtureCallStorage::class);
$fixtureStorage->addFixtureToStorage(basename(__FILE__));
