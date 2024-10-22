<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Model\Import\ProductTest;

use Magento\Catalog\Model\Indexer\Product\Price\Processor as ProductPriceIndexer;
use Magento\CatalogSearch\Model\Indexer\Fulltext as FulltextIndexer;
use Magento\CatalogImportExport\Model\Import\ProductTestBase;
use Magento\Framework\App\Area;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\TestFramework\Fixture\AppArea;
use Magento\TestFramework\Fixture\DataFixture;
use Magento\TestFramework\Fixture\DbIsolation;
use integration\framework\Magento\TestFramework\Helper\Bootstrap as BootstrapHelper;

#[
    AppArea(Area::AREA_ADMINHTML),
    DbIsolation(false),
]
class ProductIndexersInvalidationTest extends ProductTestBase
{
    #[
        DataFixture('Magento/Catalog/_files/multiple_products.php'),
    ]
    public function testIndexersState() : void
    {
        $indexerRegistry = BootstrapHelper::getObjectManager()->get(IndexerRegistry::class);
        $fulltextIndexer = $indexerRegistry->get(FulltextIndexer::INDEXER_ID);
        $priceIndexer = $indexerRegistry->get(ProductPriceIndexer::INDEXER_ID);
        $fulltextIndexer->reindexAll();
        $priceIndexer->reindexAll();

        $this->assertFalse($fulltextIndexer->isScheduled());
        $this->assertFalse($priceIndexer->isScheduled());
        $this->assertFalse($fulltextIndexer->isInvalid());
        $this->assertFalse($priceIndexer->isInvalid());

        $this->importFile('products_to_import.csv');

        $this->assertFalse($fulltextIndexer->isInvalid());
        $this->assertFalse($priceIndexer->isInvalid());
    }
}
