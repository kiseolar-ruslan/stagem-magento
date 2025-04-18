<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\MutableScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Reports\Observer\CatalogProductViewObserver;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$objectManager = Bootstrap::getObjectManager();
/** @var Session $session */
$session = $objectManager->get(Session::class);
/** @var MutableScopeConfigInterface $config */
$config = $objectManager->get(MutableScopeConfigInterface::class);
$originalValue = $config->getValue('reports/options/enabled');
/** @var CatalogProductViewObserver $reportObserver */
$reportObserver = $objectManager->get(CatalogProductViewObserver::class);

try {
    $config->setValue('reports/options/enabled', 1);
    $session->loginById(1);
    $reportObserver->execute(
        new Observer(
            [
                'event' => new DataObject(
                    [
                        'product' => new DataObject(['id' => 6]),
                    ]
                ),
            ]
        )
    );
} finally {
    $session->logout();
    $config->setValue('reports/options/enabled', $originalValue);
}

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple_rollback.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');
