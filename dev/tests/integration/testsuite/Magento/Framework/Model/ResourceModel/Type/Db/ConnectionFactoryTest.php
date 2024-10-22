<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Model\ResourceModel\Type\Db;

class ConnectionFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ConnectionFactory
     */
    private $model;

    protected function setUp(): void
    {
        $this->model = new ConnectionFactory(
            \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
        );
    }

    public function testCreate()
    {
        $dbInstance = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getInstance()
                                                                                   ->getBootstrap()
                                                                                   ->getApplication()
                                                                                   ->getDbInstance();
        $dbConfig = [
            'host' => $dbInstance->getHost(),
            'username' => $dbInstance->getUser(),
            'password' => $dbInstance->getPassword(),
            'dbname' => $dbInstance->getSchema(),
            'active' => true,
        ];
        $connection = $this->model->create($dbConfig);
        $this->assertInstanceOf(\Magento\Framework\DB\Adapter\AdapterInterface::class, $connection);
    }
}
