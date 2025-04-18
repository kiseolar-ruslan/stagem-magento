<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Customer\Model\ResourceModel\Grid;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use integration\framework\Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\Indexer\TestCase;

/**
 * Customer grid collection tests.
 */
class CollectionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $db = Bootstrap::getInstance()
            ->getBootstrap()
            ->getApplication()
            ->getDbInstance();
        if (!$db->isDbDumpExists()) {
            throw new \LogicException('DB dump does not exist.');
        }
        $db->restoreFromDbDump();

        parent::setUpBeforeClass();
    }

    /**
     * Test updated data for customer grid indexer during save/update customer data(including address data)
     * in 'Update on Schedule' mode.
     *
     * Customer Grid Indexer can't work in 'Update on Schedule' mode. All data for indexer must be updated in realtime
     * during save/update customer data(including address data).
     *
     * @magentoDataFixture Magento/Customer/_files/customer_grid_indexer_enabled_update_on_schedule.php
     * @magentoDataFixture Magento/Customer/_files/customer_sample.php
     * @magentoAppIsolation enabled
     * @magentoDbIsolation disabled
     */
    public function testGetItemByIdForUpdateOnSchedule()
    {
        $targetObject = Bootstrap::getObjectManager()
            ->create(Collection::class);
        $customerRepository = Bootstrap::getObjectManager()
            ->create(CustomerRepositoryInterface::class);
        /** Verify after first save */

        /** @var CustomerInterface $newCustomer */
        $newCustomer = $customerRepository->get('customer@example.com');
        /** @var CustomerInterface $item */
        $item = $targetObject->getItemById($newCustomer->getId());
        $this->assertNotEmpty($item);
        $this->assertSame($newCustomer->getEmail(), $item->getEmail());
        $this->assertSame('test street test city Armed Forces Middle East 01001', $item->getBillingFull());

        /** Verify after update */

        $newCustomer->setEmail('customer_updated@example.com');
        $customerRepository->save($newCustomer);
        $targetObject->clear();
        $item = $targetObject->getItemById($newCustomer->getId());
        $this->assertSame($newCustomer->getEmail(), $item->getEmail());
    }

    /**
     * Verifies that filter condition date is being converted to config timezone before select sql query
     *
     * @return void
     */
    public function testAddFieldToFilter(): void
    {
        $filterDate = "2021-01-26 00:00:00";
        /** @var TimezoneInterface $timeZone */
        $timeZone = Bootstrap::getObjectManager()
            ->get(TimezoneInterface::class);
        /** @var Collection $gridCollection */
        $gridCollection = Bootstrap::getObjectManager()
            ->get(Collection::class);
        $convertedDate = $timeZone->convertConfigTimeToUtc($filterDate);
        $collection = $gridCollection->addFieldToFilter('created_at', ['qteq' => $filterDate]);
        $expectedSelect = "WHERE (((`main_table`.`created_at` = '{$convertedDate}')))";

        $this->assertStringContainsString($expectedSelect, $collection->getSelectSql(true));
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
