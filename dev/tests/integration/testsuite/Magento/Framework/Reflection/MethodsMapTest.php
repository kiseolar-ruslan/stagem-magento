<?php
/**
 * Test case for \Magento\Framework\Profiler
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Reflection;

use integration\framework\Magento\TestFramework\Helper\CacheCleaner;

class MethodsMapTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Magento\Framework\Reflection\MethodsMap */
    private $object;

    protected function setUp(): void
    {
        $objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->object = $objectManager->create(
            \Magento\Framework\Reflection\MethodsMap::class
        );
    }

    public function testGetMethodsMap()
    {
        $data = $this->object->getMethodsMap(\Magento\Framework\Reflection\MethodsMap::class);
        $this->assertArrayHasKey('getMethodsMap', $data);
        $cachedData = $this->object->getMethodsMap(\Magento\Framework\Reflection\MethodsMap::class);
        $this->assertEquals($data, $cachedData);
    }

    public function testGetMethodParams()
    {
        $data = $this->object->getMethodParams(
            \Magento\Framework\Reflection\MethodsMap::class,
            'getMethodParams'
        );
        $this->assertCount(2, $data);
        $cachedData = $this->object->getMethodParams(
            \Magento\Framework\Reflection\MethodsMap::class,
            'getMethodParams'
        );
        $this->assertEquals($data, $cachedData);
    }
}
