<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Config\Model\Config\Backend\Image;

class AdapterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Config\Model\Config\Backend\Image\Adapter
     */
    protected $_model = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_model = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Config\Model\Config\Backend\Image\Adapter::class
        );
        $this->_model->setPath('path');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testExceptionSave()
    {
        $this->expectException(\Magento\Framework\Exception\LocalizedException::class);
        $this->expectExceptionMessage(
            'The specified image adapter cannot be used because of: Image adapter for \'wrong\' is not setup.'
        );

        $this->_model->setValue('wrong')->save();
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testCorrectSave()
    {
        $this->_model->setValue(\Magento\Framework\Image\Adapter\AdapterInterface::ADAPTER_GD2)->save();
    }
}
