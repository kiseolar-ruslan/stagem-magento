<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\DB\Transaction;
use Magento\Sales\Api\Data\OrderInterfaceFactory;
use Magento\Sales\Api\InvoiceItemRepositoryInterface;
use Magento\Sales\Api\InvoiceManagementInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Invoice\Item;
use Magento\Sales\Model\Order\Invoice\ItemFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Sales\Model\Order\Item as OrderItem;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/default_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Sales/_files/order.php');

$objectManager = Bootstrap::getObjectManager();
/** @var ProductRepositoryInterface $productRepository */
$productRepository = $objectManager->create(ProductRepositoryInterface::class);
$product = $productRepository->get('simple');
/** @var \Magento\Sales\Model\Order $order */
$order = $objectManager->get(OrderInterfaceFactory::class)->create()->loadByIncrementId('100000001');
/** @var InvoiceManagementInterface $orderService */
$orderService = $objectManager->create(InvoiceManagementInterface::class);
/** @var Invoice $invoice */
$invoice = $orderService->prepareInvoice($order);
$invoice->register();
/** @var Order $order */
$order = $invoice->getOrder();
$order->setIsInProcess(true);
/** @var Transaction $transactionSave */
$transactionSave = $objectManager->create(Transaction::class);
$transactionSave->addObject($invoice)->addObject($order)->save();

/** @var ItemFactory $invoiceItemFactory */
$invoiceItemFactory = $objectManager->create(ItemFactory::class);

$items = [
    [
        'name' => 'item 1',
        'base_price' => 10,
        'price' => 10,
        'row_total' => 10,
        'product_type' => 'simple',
        'qty' => 10,
        'qty_invoiced' => 10,
        'qty_refunded' => 1,
    ],
    [
        'name' => 'item 2',
        'base_price' => 20,
        'price' => 20,
        'row_total' => 20,
        'product_type' => 'simple',
        'qty' => 10,
        'qty_invoiced' => 10,
        'qty_refunded' => 1,
    ],
    [
        'name' => 'item 3',
        'base_price' => 30,
        'price' => 30,
        'row_total' => 30,
        'product_type' => 'simple',
        'qty' => 10,
        'qty_invoiced' => 10,
        'qty_refunded' => 1,
    ],
    [
        'name' => 'item 4',
        'base_price' => 40,
        'price' => 40,
        'row_total' => 40,
        'product_type' => 'simple',
        'qty' => 10,
        'qty_invoiced' => 10,
        'qty_refunded' => 1,
    ],
    [
        'name' => 'item 5',
        'base_price' => 50,
        'price' => 50,
        'row_total' => 50,
        'product_type' => 'simple',
        'qty' => 2,
        'qty_invoiced' => 20,
        'qty_refunded' => 2,
    ],
];

/** @var InvoiceItemRepositoryInterface $invoiceItemRepository */
$invoiceItemRepository = $objectManager->get(InvoiceItemRepositoryInterface::class);

foreach ($items as $data) {
    /** @var OrderItem $orderItem */
    $orderItem = $objectManager->create(OrderItem::class);
    $orderItem->setProductId($product->getId())->setQtyOrdered(10);
    $orderItem->setBasePrice($data['base_price']);
    $orderItem->setPrice($data['price']);
    $orderItem->setRowTotal($data['row_total']);
    $orderItem->setProductType($data['product_type']);
    $orderItem->setQtyRefunded(1);
    $orderItem->setQtyInvoiced(10);
    $orderItem->setOriginalPrice(20);

    $order->addItem($orderItem);
    $order->save();

    /** @var Item $invoiceItem */
    $invoiceItem = $invoiceItemFactory->create();
    $invoiceItem->setInvoice($invoice)
        ->setName($data['name'])
        ->setOrderItemId($orderItem->getItemId())
        ->setQty($data['qty'])
        ->setPrice($data['price']);

    $invoiceItemRepository->save($invoiceItem);
}
