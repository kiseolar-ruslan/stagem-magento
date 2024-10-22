<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Product\Type;

class VirtualTest extends \PHPUnit\Framework\TestCase
{
    public function testIsVirtual()
    {
        /** @var $model \Magento\Catalog\Model\Product\Type\Virtual */
        $model = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Product\Type\Virtual::class
        );
        $product = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Magento\Catalog\Model\Product::class
        );
        $this->assertTrue($model->isVirtual($product));
    }
}
