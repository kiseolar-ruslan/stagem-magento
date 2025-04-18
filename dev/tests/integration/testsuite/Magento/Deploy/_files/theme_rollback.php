<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
$objectManager = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var \Magento\Theme\Model\Theme $theme */
$theme = $objectManager->create(\Magento\Theme\Model\Theme::class);
$theme->load('Magento/zoom1', 'theme_path');
$theme->delete();

$theme = $objectManager->create(\Magento\Theme\Model\Theme::class);
$theme->load('Magento/zoom2', 'theme_path');
$theme->delete();

$theme = $objectManager->create(\Magento\Theme\Model\Theme::class);
$theme->load('Magento/zoom3', 'theme_path');
$theme->delete();

$theme = $objectManager->create(\Magento\Theme\Model\Theme::class);
$theme->load('Vendor/child', 'theme_path');
$theme->delete();

$theme = $objectManager->create(\Magento\Theme\Model\Theme::class);
$theme->load('Vendor/parent', 'theme_path');
$theme->delete();
