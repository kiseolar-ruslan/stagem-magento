<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search;

class GridTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlHasOnClick()
    {
        \integration\framework\Magento\TestFramework\Helper\Bootstrap::getInstance()
            ->loadArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE);
        /** @var $layout \Magento\Framework\View\LayoutInterface */
        $layout = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\View\Layout::class,
            ['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE]
        );
        $block = $layout->createBlock(
            \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option\Search\Grid::class,
            'block'
        );
        $block->setId('temp_id');

        $html = $block->toHtml();

        $regexpTemplate = '/\<script.*?\>.*?temp_id[^"]*\\.%s/is';
        $jsFuncs = ['doFilter', 'resetFilter'];
        foreach ($jsFuncs as $func) {
            $regexp = sprintf($regexpTemplate, $func);
            $this->assertMatchesRegularExpression($regexp, $html);
        }
    }
}
