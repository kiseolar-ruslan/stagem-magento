<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');
/** @var \Magento\Sales\Model\Order $order */

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$order = $objectManager->create(\Magento\Sales\Model\Order::class)->loadByIncrementId('100000001');
$order->setItems([])->setTotalItemCount(0)->save();
