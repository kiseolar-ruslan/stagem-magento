<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

\integration\framework\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    \Magento\Backend\Model\UrlInterface::class
)->turnOnSecretKey();
