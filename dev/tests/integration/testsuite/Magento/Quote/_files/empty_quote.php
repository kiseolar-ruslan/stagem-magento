<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/** @var $objectManager \Magento\TestFramework\ObjectManager */
$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/** @var \Magento\Quote\Model\Quote $quote */
$quote = $objectManager->create(\Magento\Quote\Model\Quote::class);
$quote->setStoreId(1)
    ->setIsActive(true)
    ->setIsMultiShipping(false)
    ->setReservedOrderId('reserved_order_id')
    ->collectTotals()
    ->save();

/** @var \Magento\Quote\Model\QuoteIdMask $quoteIdMask */
$quoteIdMask = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                            ->create(\Magento\Quote\Model\QuoteIdMaskFactory::class)
                                                                            ->create();
$quoteIdMask->setQuoteId($quote->getId());
$quoteIdMask->setDataChanges(true);
$quoteIdMask->save();
