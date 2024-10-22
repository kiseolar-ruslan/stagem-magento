<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $website \Magento\Store\Model\Website */
$website = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Website::class);
$website->setData(['code' => 'test', 'name' => 'Test Website', 'default_group_id' => '1', 'is_default' => '0']);
$website->save();

$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/* Refresh stores memory cache */
$objectManager->get('Magento\Store\Model\StoreManagerInterface')->reinitStores();
