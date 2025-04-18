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
use Magento\Reports\Observer\CatalogProductCompareAddProductObserver;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

Resolver::getInstance()->requireDataFixture('Magento/Catalog/_files/second_product_simple.php');
Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer.php');

$objectManager = Bootstrap::getObjectManager();
$session = $objectManager->get(Session::class);
/** @var CatalogProductCompareAddProductObserver $reportObserver */
$reportObserver = $objectManager->get(CatalogProductCompareAddProductObserver::class);
/** @var MutableScopeConfigInterface $config */
$config = $objectManager->get(MutableScopeConfigInterface::class);
$originValues = [
    'reports/options/enabled' => $config->getValue('reports/options/enabled'),
    'reports/options/product_compare_enabled' => $config->getValue('reports/options/product_compare_enabled'),
];

try {
    $config->setValue('reports/options/enabled', 1);
    $config->setValue('reports/options/product_compare_enabled', 1);
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
    foreach ($originValues as $key => $value) {
        $config->setValue($key, $value);
    }
}
