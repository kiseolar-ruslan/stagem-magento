<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Backend\Block\System\Store\Edit\Form;

/**
 * @magentoAppIsolation enabled
 * @magentoAppArea adminhtml
 */
class StoreTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Backend\Block\System\Store\Edit\Form\Store
     */
    protected $_block;

    protected function setUp(): void
    {
        parent::setUp();

        $registryData = [
            'store_type' => 'store',
            'store_data' => \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
                \Magento\Store\Model\Store::class
            ),
            'store_action' => 'add',
        ];
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        foreach ($registryData as $key => $value) {
            $objectManager->get(\Magento\Framework\Registry::class)->register($key, $value);
        }

        /** @var $layout \Magento\Framework\View\Layout */
        $layout = $objectManager->get(\Magento\Framework\View\LayoutInterface::class);

        $this->_block = $layout->createBlock(\Magento\Backend\Block\System\Store\Edit\Form\Store::class);

        $this->_block->toHtml();
    }

    protected function tearDown(): void
    {
        /** @var $objectManager \Magento\TestFramework\ObjectManager */
        $objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $objectManager->get(\Magento\Framework\Registry::class)->unregister('store_type');
        $objectManager->get(\Magento\Framework\Registry::class)->unregister('store_data');
        $objectManager->get(\Magento\Framework\Registry::class)->unregister('store_action');
    }

    public function testPrepareForm()
    {
        $form = $this->_block->getForm();
        $this->assertEquals('store_fieldset', $form->getElement('store_fieldset')->getId());
        $this->assertEquals('store_name', $form->getElement('store_name')->getId());
        $this->assertEquals('store', $form->getElement('store_type')->getValue());
    }
}
