<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\DB\Transaction;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\OrderFactory;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');
$objectManager = Bootstrap::getObjectManager();
/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->create(\Magento\Sales\Model\Order::class);
$order->loadByIncrementId('100000001');
$payment = $order->getPayment();
$billingAddress = $order->getBillingAddress();
$shippingAddress = $order->getShippingAddress();
$items = $order->getItems();
$orderItem = reset($items);
/** @var OrderFactory $orderFactory */
$orderFactory = $objectManager->get(OrderInterfaceFactory::class);
/** @var OrderRepositoryInterface $orderRepository */
$orderRepository = $objectManager->get(OrderRepositoryInterface::class);
/** @var InvoiceManagementInterface $invoiceManagement */
$invoiceManagement = $objectManager->get(InvoiceManagementInterface::class);
/** @var Transaction $transaction */
$transaction = $objectManager->get(Transaction::class);

$dateTime = new \DateTimeImmutable();
$ordersData = [
    [
        'increment_id' => '100000002',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'base_to_global_rate' => 1,
        'base_grand_total' => 120.00,
        'grand_total' => 120.00,
        'subtotal' => 120.00,
        'created_at' => $dateTime->modify('-1 hour')->format(DateTime::DATETIME_PHP_FORMAT),
    ],
    [
        'increment_id' => '100000003',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'base_to_global_rate' => 1,
        'base_grand_total' => 130.00,
        'grand_total' => 130.00,
        'subtotal' => 130.00,
        'created_at' => max($dateTime->modify('-1 day'), $dateTime->modify('first day of this month'))
            ->format(DateTime::DATETIME_PHP_FORMAT),
    ],
    [
        'increment_id' => '100000004',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'base_to_global_rate' => 1,
        'base_grand_total' => 140.00,
        'grand_total' => 140.00,
        'subtotal' => 140.00,
        'created_at' => $dateTime->modify('first day of this month')->format(DateTime::DATETIME_PHP_FORMAT),
    ],
    [
        'increment_id' => '100000005',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'base_to_global_rate' => 1,
        'base_grand_total' => 150.00,
        'grand_total' => 150.00,
        'subtotal' => 150.00,
        'created_at' => $dateTime->modify('first day of january this year')->format(DateTime::DATETIME_PHP_FORMAT),
    ],
    [
        'increment_id' => '100000006',
        'state' => Order::STATE_PROCESSING,
        'status' => 'processing',
        'base_to_global_rate' => 1,
        'base_grand_total' => 160.00,
        'grand_total' => 160.00,
        'subtotal' => 160.00,
        'created_at' => $dateTime->modify('first day of january last year')->format(DateTime::DATETIME_PHP_FORMAT),
    ],
];

foreach ($ordersData as $orderData) {
    /** @var Order $order */
    $order = $orderFactory->create();
    $order
        ->setData($orderData)
        ->addItem($orderItem)
        ->setCustomerIsGuest(true)
        ->setCustomerEmail('customer@null.com')
        ->setBillingAddress($billingAddress)
        ->setShippingAddress($shippingAddress)
        ->setPayment($payment);
    $orderRepository->save($order);

    /** @var Invoice $invoice */
    $invoice = $invoiceManagement->prepareInvoice($order);
    $invoice->register();
    $order->setIsInProcess(true);
    $transaction
        ->addObject($order)
        ->addObject($invoice)
        ->save();
}
