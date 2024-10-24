<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block;

/**
 * Test class for \Magento\Backend\Block\Widget
 *
 * @magentoAppArea adminhtml
 */
class WidgetTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @covers \Magento\Backend\Block\Widget::getButtonHtml
     */
    public function testGetButtonHtml()
    {
        $layout = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\View\Layout::class,
            ['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE]
        );
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = $layout->createBlock(\Magento\Backend\Block\Widget::class);

        $this->assertMatchesRegularExpression(
            '/\<button.*\>[\s\S]*Button Label[\s\S]*<\/button>'
                . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/is',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );
    }

    /**
     * Case when two buttons will be created in same parent block
     *
     * @covers \Magento\Backend\Block\Widget::getButtonHtml
     */
    public function testGetButtonHtmlForTwoButtonsInOneBlock()
    {
        $layout = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Framework\View\Layout::class,
            ['area' => \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE]
        );
        $layout->getUpdate()->load();
        $layout->generateXml()->generateElements();

        $widget = $layout->createBlock(\Magento\Backend\Block\Widget::class);

        $this->assertMatchesRegularExpression(
            '/<button.*\>[\s\S]*Button Label[\s\S]*<\/button>'
                . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/ius',
            $widget->getButtonHtml('Button Label', 'this.form.submit()')
        );

        $this->assertMatchesRegularExpression(
            '/<button.*\>[\s\S]*Button Label2[\s\S]*<\/button>'
                . '.*?\<script.*?\>.*?this\.form\.submit\(\).*?\<\/script\>/ius',
            $widget->getButtonHtml('Button Label2', 'this.form.submit()')
        );
    }

    public function testGetSuffixId()
    {
        $block = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
                                                                              ->create(\Magento\Backend\Block\Widget::class);
        $this->assertStringEndsNotWith('_test', $block->getSuffixId('suffix'));
        $this->assertStringEndsWith('_test', $block->getSuffixId('test'));
    }
}
