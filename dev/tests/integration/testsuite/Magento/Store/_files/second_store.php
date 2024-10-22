<?php
/**
 * Create fixture store
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Store\Model\Store $store */
$store = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
if (!$store->load('fixture_second_store', 'code')->getId()) {
    $websiteId = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        \Magento\Store\Model\StoreManagerInterface::class
    )->getWebsite()
                                                                              ->getId();
    $groupId = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
        \Magento\Store\Model\StoreManagerInterface::class
    )->getWebsite()->getDefaultGroupId();
    $store->setCode(
        'fixture_second_store'
    )->setWebsiteId(
        $websiteId
    )->setGroupId(
        $groupId
    )->setName(
        'Fixture Store'
    )->setSortOrder(
        10
    )->setIsActive(
        1
    );
    $store->save();
}
