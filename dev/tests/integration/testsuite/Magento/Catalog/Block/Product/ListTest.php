<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Catalog\Block\Product;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * Test class for \Magento\Catalog\Block\Product\List.
 *
 * @magentoDataFixture Magento/Catalog/_files/product_simple.php
 * @magentoAppArea frontend
 * @magentoDbIsolation disabled
 */
class ListTest extends TestCase
{
    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $_block;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection|MockObject
     */
    private $collectionProductMock;

    protected function setUp(): void
    {
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\App\State::class)
            ->setAreaCode('frontend');
        $this->_block = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\LayoutInterface::class
        )->createBlock(
            \Magento\Catalog\Block\Product\ListProduct::class
        );

        $this->collectionProductMock = $this->createMock(\Magento\Catalog\Model\ResourceModel\Product\Collection::class);
    }

    public function testGetLayer()
    {
        $this->assertInstanceOf(\Magento\Catalog\Model\Layer::class, $this->_block->getLayer());
    }

    public function testGetLoadedProductCollection()
    {
        $this->_block->setShowRootCategory(true);
        $collection = $this->_block->getLoadedProductCollection();
        $this->assertInstanceOf(\Magento\Catalog\Model\ResourceModel\Product\Collection::class, $collection);
        /* Check that root category was defined for Layer as current */
        $this->assertEquals(2, $this->_block->getLayer()->getCurrentCategory()->getId());
        $this->collectionProductMock->expects($this->never())->method('load');
    }

    /**
     * @covers \Magento\Catalog\Block\Product\ListProduct::getToolbarBlock
     * @covers \Magento\Catalog\Block\Product\ListProduct::getMode
     * @covers \Magento\Catalog\Block\Product\ListProduct::getToolbarHtml
     * @covers \Magento\Catalog\Block\Product\ListProduct::toHtml
     */
    public function testToolbarCoverage()
    {
        /** @var $parent \Magento\Catalog\Block\Product\ListProduct */
        $parent = $this->_getLayout()->createBlock(\Magento\Catalog\Block\Product\ListProduct::class, 'parent');

        /* Prepare toolbar block */
        $this->_getLayout()
            ->createBlock(\Magento\Catalog\Block\Product\ProductList\Toolbar::class, 'product_list_toolbar');
        $parent->setToolbarBlockName('product_list_toolbar');

        $toolbar = $parent->getToolbarBlock();
        $this->assertInstanceOf(\Magento\Catalog\Block\Product\ProductList\Toolbar::class, $toolbar, 'Default Toolbar');

        $parent->setChild('toolbar', $toolbar);
        /* In order to initialize toolbar collection block toHtml should be called before toolbar toHtml */
        $this->assertEmpty($parent->toHtml(), 'Block HTML'); /* Template not specified */
        $this->assertEquals('grid', $parent->getMode(), 'Default Mode'); /* default mode */
        $this->assertNotEmpty($parent->getToolbarHtml(), 'Toolbar HTML'); /* toolbar for one simple product */
    }

    public function testGetAdditionalHtmlEmpty()
    {
        $this->_block->setLayout($this->_getLayout());
        $this->assertEmpty($this->_block->getAdditionalHtml());
    }

    public function testGetAdditionalHtml()
    {
        $layout = $this->_getLayout();
        /** @var $parent \Magento\Catalog\Block\Product\ListProduct */
        $parent = $layout->createBlock(\Magento\Catalog\Block\Product\ListProduct::class);
        $childBlock = $layout->createBlock(
            \Magento\Framework\View\Element\Text::class,
            'test',
            ['data' => ['text' => 'test']]
        );
        $layout->setChild($parent->getNameInLayout(), $childBlock->getNameInLayout(), 'additional');
        $this->assertEquals('test', $parent->getAdditionalHtml());
    }

    public function testSetCollection()
    {
        $collection = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                                   ->create(\Magento\Framework\Data\Collection::class);
        $this->_block->setCollection($collection);
        $this->assertEquals($collection, $this->_block->getLoadedProductCollection());
    }

    public function testGetPriceBlockTemplate()
    {
        $this->assertNull($this->_block->getPriceBlockTemplate());
        $this->_block->setData('price_block_template', 'test');
        $this->assertEquals('test', $this->_block->getPriceBlockTemplate());
    }

    public function testPrepareSortableFieldsByCategory()
    {
        /** @var $category \Magento\Catalog\Model\Category */
        $category = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Category::class
        );
        $category->setDefaultSortBy('name');
        $this->_block->prepareSortableFieldsByCategory($category);
        $this->assertEquals('name', $this->_block->getSortBy());
    }

    protected function _getLayout()
    {
        return \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\View\LayoutInterface::class
        );
    }
}
