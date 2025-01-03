<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');
/** @var Order $order */

$objectManager = Bootstrap::getObjectManager();
/** @var OrderInterface $order */
$order = Bootstrap::getObjectManager()->create(OrderInterface::class)->load('100000001', 'increment_id');
$invoices = [
    [
        'store_id' => 1,
        'grand_total' =>  280.00,
        'order_id' => $order->getId(),
        'email_sent' => 0,
        'send_email' => 0,
        'increment_id' => '123',
        'can_void_flag' => 1,
        'state'     => 1
    ],
    [
        'store_id' => 1,
        'grand_total' =>  450.00,
        'order_id' => $order->getId(),
        'email_sent' => 1,
        'send_email' => 1,
        'increment_id' => '456',
        'can_void_flag' => 1,
        'state'     => 1
    ],
    [
        'store_id' => 0,
        'grand_total' =>  10.00,
        'order_id' => $order->getId(),
        'email_sent' => 1,
        'send_email' => 1,
        'increment_id' => '789',
        'can_void_flag' => 0,
        'state'     => 1
    ],
    [
        'store_id' => 1,
        'grand_total' =>  1110.00,
        'order_id' => $order->getId(),
        'email_sent' => 1,
        'increment_id' => '012',
        'send_email' => 1,
        'can_void_flag' => 1,
        'state'     => 0
    ],
];

/** @var array $invoiceData */
foreach ($invoices as $invoiceData) {
    /** @var \Magento\Sales\Model\Order\Invoice $invoice */
    $invoice = $objectManager->create(\Magento\Sales\Model\Order\Invoice::class);
    $invoice
        ->setData($invoiceData)
        ->save();
}
