<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Backend\Block\Widget;

use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Magento\Framework\View\LayoutInterface;

/**
 * Test for the button widget.
 *
 * @magentoAppArea adminhtml
 */
class ButtonTest extends TestCase
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $objectManager = Bootstrap::getObjectManager();
        $this->layout = $objectManager->get(LayoutInterface::class);
    }

    /**
     * Create the block.
     *
     * @return Button
     */
    private function createBlock(): Button
    {
        /** @var Button $block */
        $block = $this->layout->createBlock(Button::class, 'button_block');
        $block->setLayout($this->layout);

        return $block;
    }

    /**
     * Test resulting button HTML.
     *
     * @return void
     */
    public function testToHtml(): void
    {
        $block = $this->createBlock();
        $block->addData(
            [
                'type' => 'button',
                'onclick' => 'console.log("Button pressed!")',
                'disabled' => false,
                'title' => 'A button',
                'label' => 'A button',
                'class' => 'button',
                'id' => 'button',
                'element_name' => 'some-name',
                'value' => 'Press a button',
                'data-style' => 'width: 100px',
                'style' => 'height: 200px'
            ]
        );

        $html = $block->toHtml();
        $this->assertStringContainsString('<button ', $html);
        $this->assertStringContainsString('<span>A button</span>', $html);
        $this->assertStringNotContainsString('onclick=', $html);
        $this->assertStringNotContainsString('style=', $html);
        $this->assertMatchesRegularExpression('/\<script.*?\>.*?' .preg_quote($block->getOnClick()) .'.*?\<\/script\>/ims', $html);
        $this->assertStringContainsString('height', $html);
        $this->assertStringContainsString('200px', $html);
    }
}
