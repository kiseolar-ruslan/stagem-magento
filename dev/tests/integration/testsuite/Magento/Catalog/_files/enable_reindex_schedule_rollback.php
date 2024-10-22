<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* @var \Magento\Framework\Indexer\IndexerInterface $model */
$model = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    \Magento\Framework\Indexer\IndexerRegistry::class
)->get('catalogsearch_fulltext');
$model->setScheduled(false);
