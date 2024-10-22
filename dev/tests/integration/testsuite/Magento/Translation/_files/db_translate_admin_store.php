<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

\integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    \Magento\Framework\App\AreaList::class
)->getArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
)->load(
    \Magento\Framework\App\Area::PART_CONFIG
);
/** @var \Magento\Translation\Model\ResourceModel\StringUtils $translateString */
$translateString = \integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    \Magento\Translation\Model\ResourceModel\StringUtils::class
);
$translateString->saveTranslate('string to translate', 'predefined string translation', null);
