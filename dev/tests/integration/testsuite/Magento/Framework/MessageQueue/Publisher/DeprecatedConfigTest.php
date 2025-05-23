<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\MessageQueue\Publisher;

use Magento\Framework\MessageQueue\DefaultValueProvider;

/**
 * Test access to publisher configuration declared in deprecated queue.xml configs using Publisher\ConfigInterface.
 *
 * @magentoCache config disabled
 */
class DeprecatedConfigTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var DefaultValueProvider
     */
    private $defaultValueProvider;

    protected function setUp(): void
    {
        $this->objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->defaultValueProvider = $this->objectManager->get(DefaultValueProvider::class);
    }

    public function testGetPublisher()
    {
        /** @var \Magento\Framework\MessageQueue\Publisher\ConfigInterface $config */
        $config = $this->objectManager->create(\Magento\Framework\MessageQueue\Publisher\ConfigInterface::class);
        $publisher = $config->getPublisher('deprecated.config.async.string.topic');
        $this->assertEquals('deprecated.config.async.string.topic', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals($this->defaultValueProvider->getConnection(), $connection->getName());
        $this->assertEquals('magento', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }

    public function testGetPublisherCustomConnection()
    {
        /** @var \Magento\Framework\MessageQueue\Publisher\ConfigInterface $config */
        $config = $this->objectManager->create(\Magento\Framework\MessageQueue\Publisher\ConfigInterface::class);
        $publisher = $config->getPublisher('deprecated.config.sync.bool.topic');
        $this->assertEquals('deprecated.config.sync.bool.topic', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals('db', $connection->getName());
        $this->assertEquals('customExchange', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }

    public function testGetOverlapWithQueueConfig()
    {
        /** @var \Magento\Framework\MessageQueue\Publisher\ConfigInterface $config */
        $config = $this->objectManager->create(\Magento\Framework\MessageQueue\Publisher\ConfigInterface::class);
        $publisher = $config->getPublisher('overlapping.topic.declaration');
        $this->assertEquals('overlapping.topic.declaration', $publisher->getTopic());
        $this->assertFalse($publisher->isDisabled());

        $connection = $publisher->getConnection();
        $this->assertEquals($this->defaultValueProvider->getConnection(), $connection->getName());
        $this->assertEquals('magento', $connection->getExchange());
        $this->assertFalse($connection->isDisabled());
    }
}
