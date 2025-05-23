<?php
/**
 * Create fixture store with code test
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/** @var $store \Magento\Store\Model\Store */
$store = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
if (!$store->load('test', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'test',
            'website_id' => '1',
            'group_id' => '1',
            'name' => 'Test Store',
            'sort_order' => '0',
            'is_active' => '1',
        ]
    );
    $store->save();
} else {
    if ($store->getId()) {
        /** @var \integration\framework\Magento\TestFramework\Helper\Bootstrap $registry */
        $registry = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            \Magento\Framework\Registry::class
        );
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);

        $store->delete();

        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);

        $store = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(\Magento\Store\Model\Store::class);
        $store->setData(
            [
                'code' => 'test',
                'website_id' => '1',
                'group_id' => '1',
                'name' => 'Test Store',
                'sort_order' => '0',
                'is_active' => '1',
            ]
        );
        $store->save();
    }
}
