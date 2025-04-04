<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

use Magento\Framework\Module\Manager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Security\Model\ResourceModel\PasswordResetRequestEvent;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Workaround\Override\Fixture\Resolver;

$objectManager = Bootstrap::getObjectManager();
/** @var Manager $moduleManager */
$moduleManager = $objectManager->get(Manager::class);
//This check is needed because Magento_Security independent of Magento_Customer
if ($moduleManager->isEnabled('Magento_Customer')) {
    Resolver::getInstance()->requireDataFixture('Magento/Customer/_files/customer_rollback.php');

    /** @var PasswordResetRequestEvent $passwordResetRequestEventResource */
    $passwordResetRequestEventResource = $objectManager->get(PasswordResetRequestEvent::class);
    $dateTime = new DateTimeImmutable();
    $passwordResetRequestEventResource->deleteRecordsOlderThen($dateTime->format(DateTime::DATETIME_PHP_FORMAT));
}
