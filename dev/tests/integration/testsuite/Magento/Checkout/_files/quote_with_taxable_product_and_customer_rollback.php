<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Quote\Api\CartRepositoryInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Quote\Model\GetQuoteByReservedOrderId;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var CartRepositoryInterface $quoteRepository */
$quoteRepository = $objectManager->get(CartRepositoryInterface::class);
/** @var GetQuoteByReservedOrderId $getQuoteByReservedOrderId */
$getQuoteByReservedOrderId = $objectManager->get(GetQuoteByReservedOrderId::class);
$quote = $getQuoteByReservedOrderId->execute('test_order_with_taxable_product');
if ($quote) {
    $quoteRepository->delete($quote);
}

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/taxable_simple_product_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_address_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
