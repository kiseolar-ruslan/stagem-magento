<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
$indexer = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Indexer\Model\Indexer::class
);
$indexer->load('catalogsearch_fulltext');
$indexer->reindexAll();
