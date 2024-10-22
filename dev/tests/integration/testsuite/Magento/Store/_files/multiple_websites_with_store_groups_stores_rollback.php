<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var \Magento\Framework\Registry $registry */
$registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(\Magento\Framework\Registry::class);

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', true);
/** Delete the second website **/
$website = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Website::class);
/** @var $website \Magento\Store\Model\Website */
$websiteId = $website->load('second', 'code')->getId();
if ($websiteId) {
    $website->delete();
}

/** Delete the third website **/
$website2 = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Website::class);
/** @var $website \Magento\Store\Model\Website */
$websiteId2 = $website2->load('third', 'code')->getId();
if ($websiteId2) {
    $website2->delete();
}

/** Delete the second store groups **/
$group = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Group::class);
/** @var $group \Magento\Store\Model\Group */
$groupId = $group->load('second_store', 'code')->getId();
if ($groupId) {
    $group->delete();
}

/** Delete the third store groups **/
$group2 = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Group::class);
/** @var $group2 \Magento\Store\Model\Group */
$groupId2 = $group2->load('third_store', 'code')->getId();
if ($groupId2) {
    $group2->delete();
}

/** Delete the second store **/
$store = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
if ($store->load('second_store_view', 'code')->getId()) {
    $store->delete();
}

/** Delete the third store **/
$store2 = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
if ($store2->load('third_store_view', 'code')->getId()) {
    $store2->delete();
}

$registry->unregister('isSecureArea');
$registry->register('isSecureArea', false);
