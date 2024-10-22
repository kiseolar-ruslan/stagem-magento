<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Indexer\Product\Price\Action;

/**
 * Class RowsTest
 */
class RowsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $_processor;

    protected function setUp(): void
    {
        $this->_product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Product::class
        );
        $this->_processor = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Indexer\Product\Price\Processor::class
        );
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/price_row_fixture.php
     */
    public function testProductsUpdate()
    {
        /** @var \Magento\Catalog\Api\ProductRepositoryInterface $productRepository */
        $product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Catalog\Model\Product::class
        );
        /** @var \Magento\Catalog\Api\CategoryLinkManagementInterface $linkManagment */
        $linkManagment = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Catalog\Api\CategoryLinkManagementInterface::class
        );
        $this->_product = $product->load(1);
        $linkManagment->assignProductToCategories($this->_product->getSku(), [9]);
        $this->_processor->reindexList([$this->_product->getId()]);

        $categoryFactory = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Catalog\Model\CategoryFactory::class
        );
        $listProduct = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Catalog\Block\Product\ListProduct::class
        );

        $category = $categoryFactory->create()->load(9);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();

        $this->assertEquals(1, $productCollection->count());
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $this->assertEquals($this->_product->getId(), $product->getId());
            $this->assertEquals($this->_product->getPrice(), $product->getPrice());
        }
    }
}
